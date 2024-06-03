/* eslint-disable react/prop-types */
import { Fragment, useContext } from "react";
import useAES from "../hooks/useAES";
import Unauthorized from "../pages/errors/Unauthorized";
import { AuthContext } from "../context/AuthContext";

export default function RolesMiddleware({
  children,
  roles,
  unauthorized = true,
}) {
  const { jwt } = useContext(AuthContext);
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
