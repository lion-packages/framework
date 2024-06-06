/* eslint-disable react/prop-types */
import axios from "axios";
import { jwtDecode } from "jwt-decode";
import { createContext, useEffect, useState } from "react";
import { useNavigate } from "react-router-dom";

export const AuthContext = createContext();

export function AuthProvider({ children }) {
  const navigate = useNavigate();

  const [jwt, setJwt] = useState(null);
  const [refresh, setRefresh] = useState(null);
  const [loadingJWT, setLoadingJWT] = useState(true);

  const login = (tokan_access, token_refresh) => {
    setLoadingJWT(true);

    setJwt(tokan_access);

    setRefresh(token_refresh);

    sessionStorage.setItem("jwt", tokan_access);

    sessionStorage.setItem("refresh", token_refresh);

    setLoadingJWT(false);
  };

  const logout = () => {
    setLoadingJWT(true);

    setJwt(null);

    setRefresh(null);

    sessionStorage.removeItem("jwt");

    sessionStorage.removeItem("refresh");

    setLoadingJWT(false);
  };

  const refreshToken = async () => {
    try {
      const form = { jwt_refresh: getRefresh() };

      const res = await axios.post(
        import.meta.env.VITE_SERVER_URL_AUD + "/api/auth/refresh",
        form,
        {
          headers: {
            "Content-Type": "application/json",
            Authorization: `Bearer ${getJWT()}`,
          },
        }
      );

      if (res.data) {
        login(res.data.data.jwt_access, res.data.data.jwt_refresh);
      }

      if (res.response) {
        logout();

        navigate("/auth/login");
      }

      return res.data.data.jwt_access;
    } catch (error) {
      console.error("Error refreshing token:", error);

      throw error;
    }
  };

  const getData = () => {
    return jwtDecode(jwt);
  };

  const getJWT = () => {
    return sessionStorage.getItem("jwt");
  };

  const getRefresh = () => {
    return sessionStorage.getItem("refresh");
  };

  useEffect(() => {
    const storedJwt = sessionStorage.getItem("jwt");
    const storedRefresh = sessionStorage.getItem("refresh");

    if (storedJwt) {
      setJwt(storedJwt);
    }

    if (storedRefresh) {
      setRefresh(storedRefresh);
    }

    setLoadingJWT(false);
  }, []);

  return (
    <AuthContext.Provider
      value={{
        jwt,
        refresh,
        loadingJWT,
        login,
        logout,
        getData,
        getJWT,
        getRefresh,
        refreshToken,
      }}
    >
      {children}
    </AuthContext.Provider>
  );
}
