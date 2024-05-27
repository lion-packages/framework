/* eslint-disable react/prop-types */
import { Navigate } from "react-router-dom";
import { useAuth } from "../context/AuthProvider";
import LoadingScreen from "../pages/components/LoadingScreen";

export default function AuthenticatedMiddleware({ children }) {
  const { jwt, loadingJWT } = useAuth();

  if (loadingJWT) {
    return <LoadingScreen />;
  }

  if ([null, undefined].includes(jwt)) {
    return <Navigate to="/auth/login" replace />;
  }

  return children;
}
