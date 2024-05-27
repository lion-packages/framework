/* eslint-disable react/prop-types */
import Unauthorized from "../pages/errors/Unauthorized.jsx";
import { useAuth } from "../context/AuthProvider.jsx";
import { Fragment } from "react";

export default function RolesMiddleware({
  children,
  roles,
  unauthorized = true,
}) {
  const { jwt } = useAuth();

  return jwt.idroles && !roles.includes(jwt.idroles) ? (
    unauthorized ? (
      <Unauthorized />
    ) : (
      <Fragment />
    )
  ) : (
    children
  );
}
