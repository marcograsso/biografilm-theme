import "mapbox-gl/dist/mapbox-gl.css";
import mapboxgl from "mapbox-gl";
import Alpine from "alpinejs";
import mapPinSvg from "../../svgs/generic/map-pin.svg?raw";

Alpine.data("LocationsMap", (locations) => {
  let map = null;
  let markers = [];

  return {
    locations,

    init() {
      const withCoords = this.locations.filter(
        (l) => l.latitude && l.longitude,
      );
      if (!withCoords.length || !window.mapboxKey) return;

      mapboxgl.accessToken = window.mapboxKey;

      map = new mapboxgl.Map({
        container: this.$refs.map,
        style: "mapbox://styles/mapbox/light-v11",
        center: [withCoords[0].longitude, withCoords[0].latitude],
        zoom: 14,
        scrollZoom: false,
      });

      map.on("load", () => {
        this.addMarkers(withCoords);
        this.fitMarkers(withCoords);
      });
    },

    addMarkers(locs) {
      locs.forEach((location) => {
        const el = document.createElement("div");
        el.className = "locations-map-marker";
        el.innerHTML = mapPinSvg;

        const popupContent = [
          `<strong>${location.name}</strong>`,
          location.address ? `<p>${location.address}</p>` : "",
          location.link
            ? `<a href="${location.link}" target="_blank">Indicazioni</a>`
            : "",
        ]
          .filter(Boolean)
          .join("");

        const popup = new mapboxgl.Popup({
          offset: [0, -50],
          closeButton: false,
          className: "locations-map-popup",
        }).setHTML(popupContent);

        const marker = new mapboxgl.Marker({ element: el, anchor: "bottom" })
          .setLngLat([location.longitude, location.latitude])
          .setPopup(popup)
          .addTo(map);

        el.addEventListener("click", () => {
          marker.togglePopup();
          map.flyTo({ center: [location.longitude, location.latitude], zoom: 16, duration: 600 });
        });

        markers.push(marker);
      });
    },

    zoomIn() {
      map?.zoomIn();
    },

    zoomOut() {
      map?.zoomOut();
    },

    fitMarkers(locs) {
      if (locs.length === 1) {
        map.flyTo({ center: [locs[0].longitude, locs[0].latitude], zoom: 14 });
        return;
      }

      const bounds = locs.reduce(
        (b, l) => b.extend([l.longitude, l.latitude]),
        new mapboxgl.LngLatBounds(
          [locs[0].longitude, locs[0].latitude],
          [locs[0].longitude, locs[0].latitude],
        ),
      );

      map.fitBounds(bounds, { padding: 120, maxZoom: 14 });
    },
  };
});
