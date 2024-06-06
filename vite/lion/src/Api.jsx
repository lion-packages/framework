import axios from "axios";

export default function axiosApi(refreshToken) {
  const api = axios.create({
    baseURL: import.meta.env.VITE_SERVER_URL_AUD,
    headers: {
      "Content-Type": "application/json",
    },
  });

  api.interceptors.request.use(
    (config) => {
      const token = sessionStorage.getItem("jwt");

      if (token) {
        config.headers["Authorization"] = `Bearer ${token}`;
      }

      return config;
    },
    (error) => {
      return Promise.reject(error);
    }
  );

  api.interceptors.response.use(
    (response) => response,
    async (err) => {
      const originalRequest = err.config;

      if (
        401 === err.response.data.code &&
        "session-error" === err.response.data.status &&
        !originalRequest._retry
      ) {
        originalRequest._retry = true;

        try {
          const newToken = await refreshToken();

          axios.defaults.headers.common["Authorization"] = "Bearer " + newToken;

          originalRequest.headers["Authorization"] = "Bearer " + newToken;

          return api(originalRequest);
        } catch (e) {
          return Promise.reject(e);
        }
      }

      return Promise.reject(err);
    }
  );

  return api;
}
