import Unauthorized from "../pages/errors/Unauthorized.jsx";
import { useAuth } from "../context/AuthProvider.jsx";
import { Fragment } from "react";

// eslint-disable-next-line react/prop-types
export default function RolesMiddleware({ children, roles, unauthorized = true }) {
  const { jwt } = useAuth();

  return jwt.idroles && !roles.includes(jwt.idroles) ? (unauthorized ? <Unauthorized /> : <Fragment />) : children;
}
