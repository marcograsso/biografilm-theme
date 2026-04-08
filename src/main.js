import "non.geist";
import "non.geist/mono";

import Alpine from "alpinejs";
import collapse from "@alpinejs/collapse";

Alpine.plugin(collapse);

import "./main.css";

import.meta.glob("../views/**/*.js", { eager: true });
import.meta.glob("../views/**/*.css", { eager: true });

window.Alpine = Alpine;
Alpine.start();
