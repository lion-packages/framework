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
  const [auth_2fa, setAuth_2fa] = useState(false);

  const login = (tokan_access, token_refresh, auth_2fa) => {
    setLoadingJWT(true);

    setJwt(tokan_access);

    setRefresh(token_refresh);

    setAuth_2fa(auth_2fa.auth_2fa);

    sessionStorage.setItem("jwt", tokan_access);

    sessionStorage.setItem("refresh", token_refresh);

    sessionStorage.setItem("auth_2fa", JSON.stringify(auth_2fa));

    setLoadingJWT(false);
  };

  const logout = () => {
    setLoadingJWT(true);

    setJwt(null);

    setRefresh(null);

    setAuth_2fa(false);

    sessionStorage.removeItem("jwt");

    sessionStorage.removeItem("refresh");

    sessionStorage.removeItem("auth_2fa");

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

    if (storedJwt) {
      setJwt(storedJwt);
    }

    const storedRefresh = sessionStorage.getItem("refresh");

    if (storedRefresh) {
      setRefresh(storedRefresh);
    }

    const auth_2fa = sessionStorage.getItem("auth_2fa");

    if (auth_2fa) {
      setAuth_2fa(JSON.parse(auth_2fa).auth_2fa);
    }

    setLoadingJWT(false);
  }, []);

  return (
    <AuthContext.Provider
      value={{
        jwt,
        refresh,
        auth_2fa,
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
