/* eslint-disable react/prop-types */
import { Navigate } from "react-router-dom";
import LoadingScreen from "../components/LoadingScreen";
import { useContext } from "react";
import { AuthContext } from "../context/AuthContext";

export default function NotAuthenticatedMiddleware({ children }) {
  const { jwt, loadingJWT } = useContext(AuthContext);

  if (loadingJWT) {
    return <LoadingScreen />;
  }

  if (jwt) {
    return <Navigate to="/dashboard" replace />;
  }

  return children;
}
