import React from "react";
import ReactDOM from "react-dom/client";
import App from "./App.jsx";
import { BrowserRouter } from "react-router-dom";
import "./assets/css/index.css";
import { AuthProvider } from "./context/AuthContext.jsx";
import { ResponseProvider } from "./context/ResponseContext.jsx";

ReactDOM.createRoot(document.getElementById("root")).render(
  <BrowserRouter>
    <AuthProvider>
      <ResponseProvider>
        <App />
      </ResponseProvider>
    </AuthProvider>
  </BrowserRouter>
);
