/* eslint-disable react/prop-types */
import { Fragment, useContext } from "react";
import useAES from "../hooks/useAES";
import Unauthorized from "../pages/errors/Unauthorized";
import { AuthContext } from "../context/AuthContext";
import { jwtDecode } from "jwt-decode";

export default function RolesMiddleware({
  children,
  roles,
  unauthorized = true,
}) {
  const { jwt } = useContext(AuthContext);
  const { decode } = useAES();

  return jwt &&
    !roles.includes(parseInt(decode(jwtDecode(jwt).data.idroles))) ? (
    unauthorized ? (
      <Unauthorized />
    ) : (
      <Fragment />
    )
  ) : (
    children
  );
}
