import { Navigate } from "react-router-dom";
import { useAuth } from "../context/AuthProvider";

export default function NotAuthenticatedMiddleware({ children }) {
  const { jwt } = useAuth();

  if (jwt) {
    return <Navigate to="/dashboard" replace />;
  }

  return children;
}
