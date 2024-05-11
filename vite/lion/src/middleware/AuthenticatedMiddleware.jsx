import { Navigate } from "react-router-dom";
import { useAuth } from "../context/AuthProvider";

export default function AuthenticatedMiddleware({ children }) {
  const { jwt } = useAuth();

  if ([null, undefined].includes(jwt)) {
    return <Navigate to="/auth/login" replace />;
  }

  return children;
}
