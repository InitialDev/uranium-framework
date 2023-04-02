import React from "react";
import { createRoot } from "react-dom/client";
import "bootstrap";

// Custom styles
import "./styles/core.scss"

import _Main from "./react/core.jsx";
const root = createRoot(document.getElementById("root"));
root.render(<_Main />);
