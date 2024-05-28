/* eslint-disable react/prop-types */
import Unauthorized from "../pages/errors/Unauthorized.jsx";
import { useAuth } from "../context/AuthProvider.jsx";
import { Fragment } from "react";
import useAES from "../hooks/useAES.jsx";

export default function RolesMiddleware({
  children,
  roles,
  unauthorized = true,
}) {
  const { jwt } = useAuth();
  const { decode } = useAES();

  return jwt.idroles && !roles.includes(parseInt(decode(jwt.idroles))) ? (
    unauthorized ? (
      <Unauthorized />
    ) : (
      <Fragment />
    )
  ) : (
    children
  );
}
