/* eslint-disable react/prop-types */
import { Navigate } from "react-router-dom";
import LoadingScreen from "../components/LoadingScreen";
import { useContext } from "react";
import { AuthContext } from "../context/AuthContext";

export default function AuthenticatedMiddleware({ children }) {
  const { jwt, loadingJWT } = useContext(AuthContext)();

  if (loadingJWT) {
    return <LoadingScreen />;
  }

  if ([null, undefined].includes(jwt)) {
    return <Navigate to="/auth/login" replace />;
  }

  return children;
}
