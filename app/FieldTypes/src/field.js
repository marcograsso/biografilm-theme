const $ = jQuery;

// Tracks whichever MapboxField instance currently has focus in its address input.
// Only one autofill collection is created globally so all repeater rows share it.
let activeMapboxField = null;

class MapboxField {
  constructor($field) {
    this.$field = $field;

    const $container = $field.find(".acf-mapbox-field");
    this.apiKey = $container.data("api-key");
    this.country = $container.data("country") || "it";

    this.$address = $field.find(".acf-mapbox-address-value");
    this.$lat = $field.find(".acf-mapbox-latitude-value");
    this.$lng = $field.find(".acf-mapbox-longitude-value");
    this.$minimap = $field.find("mapbox-address-minimap")[0] || null;

    this.init();
  }

  init() {
    if (!this.apiKey) {
      console.error("Mapbox: API key missing on field");
      return;
    }

    // Mark this instance as active when its address input gains focus
    this.$address.on("focus", () => {
      activeMapboxField = this;
    });

    // Create the global autofill collection only once
    if (!window._mapboxAutofillCollection) {
      window._mapboxAutofillCollection = mapboxsearch.autofill({
        accessToken: this.apiKey,
        options: { country: this.country },
      });

      window._mapboxAutofillCollection.addEventListener(
        "retrieve",
        (event) => {
          if (!activeMapboxField) return;
          activeMapboxField.handleRetrieve(event);
        },
      );
    }

    this.initMinimap();
  }

  handleRetrieve(event) {
    const feature = event.detail.features[0];
    if (!feature?.geometry?.coordinates) return;

    const [lng, lat] = feature.geometry.coordinates;
    const address =
      feature.properties.full_address || feature.properties.name || "";

    this.$address.val(address);
    this.$lat.val(lat);
    this.$lng.val(lng);

    if (this.$minimap) {
      this.$minimap.feature = feature;
      this.$minimap.showMarker = true;
    }

    // Notify ACF that these fields changed
    this.$address.trigger("change");
    this.$lat.trigger("change");
    this.$lng.trigger("change");

    // Close the suggestions listbox
    setTimeout(() => this.$address[0].blur(), 50);

    activeMapboxField = null;
  }

  initMinimap() {
    if (!this.$minimap) return;

    this.$minimap.accessToken = this.apiKey;
    this.$minimap.theme = {
      variables: {
        border: "1px solid #bbb",
        borderRadius: "4px",
        boxShadow: "0 2px 8px rgba(0,0,0,0.15)",
      },
    };

    // Default center: Bologna
    this.$minimap.feature = {
      type: "Feature",
      geometry: { type: "Point", coordinates: [11.355563, 44.488245] },
      properties: {},
    };

    // Restore existing saved coordinates
    const lat = this.$lat.val();
    const lng = this.$lng.val();
    const address = this.$address.val();

    if (lat && lng && address) {
      this.$minimap.feature = {
        type: "Feature",
        geometry: {
          type: "Point",
          coordinates: [parseFloat(lng), parseFloat(lat)],
        },
        properties: { full_address: address },
      };
      this.$minimap.showMarker = true;
    }

    // Allow dragging the pin to adjust coordinates
    this.$minimap.addEventListener("saveMarkerLocation", (event) => {
      if (!event.detail?.coordinates) return;
      const { lat, lng } = event.detail.coordinates;
      this.$lat.val(lat);
      this.$lng.val(lng);
      this.$lat.trigger("change");
    });
  }
}

if (typeof acf !== "undefined" && typeof acf.add_action !== "undefined") {
  acf.add_action("ready append", ($el) => {
    acf.get_fields({ type: "mapbox" }, $el).each(function () {
      new MapboxField($(this));
    });
  });
}
