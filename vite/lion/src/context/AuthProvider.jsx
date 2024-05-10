import { jwtDecode } from "jwt-decode";
import { createContext, useContext, useEffect, useState } from "react";

const AuthContext = createContext();

export function AuthProvider({ children }) {
  const [jwt, setJwt] = useState(null);

  const login = (jwtObject) => {
    setJwt(jwtDecode(jwtObject).data);

    sessionStorage.setItem("jwt", JSON.stringify(jwtObject));
  };

  const logout = () => {
    setJwt(null);

    sessionStorage.removeItem("jwt");
  };

  const getData = () => {
    return jwtDecode(jwt);
  };

  const getJWT = () => {
    return JSON.parse(sessionStorage.getItem("jwt"));
  };

  useEffect(() => {
    const storedJwt = JSON.parse(sessionStorage.getItem("jwt"));

    if (storedJwt) {
      setJwt(jwtDecode(storedJwt).data);
    }
  }, []);

  return (
    <AuthContext.Provider value={{ jwt, login, logout, getData, getJWT }}>
      {children}
    </AuthContext.Provider>
  );
}

export function useAuth() {
  return useContext(AuthContext);
}