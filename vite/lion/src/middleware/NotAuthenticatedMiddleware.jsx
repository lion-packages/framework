/* eslint-disable react/prop-types */
import { Navigate } from "react-router-dom";
import { useAuth } from "../context/AuthProvider";
import LoadingScreen from "../pages/components/LoadingScreen";

export default function NotAuthenticatedMiddleware({ children }) {
  const { jwt, loadingJWT } = useAuth();

  if (loadingJWT) {
    return <LoadingScreen />;
  }

  if (jwt) {
    return <Navigate to="/dashboard" replace />;
  }

  return children;
}
