/* eslint-disable react-hooks/exhaustive-deps */
/* eslint-disable react/prop-types */
import { createContext, useContext, useEffect, useState } from "react";
import { ResponseContext } from "../ResponseContext";
import { AuthContext } from "../AuthContext";
import axiosApi from "../../Api";

export const UsersContext = createContext();

export function UsersProvider({ children }) {
  const { addToast } = useContext(ResponseContext);
  const { refreshToken } = useContext(AuthContext);

  const [users, setUsers] = useState([]);

  const handleReadUsers = async () => {
    const res = await axiosApi(refreshToken).get(
      `${import.meta.env.VITE_SERVER_URL_AUD}/api/users`
    );

    if (res.data && !res.data.status) {
      setUsers(res.data);
    }

    if (res.response && 403 === res.response.data.code) {
      console.log(res);
      addToast([
        {
          status: res.response.data.status,
          title: "Users",
          message: res.response.data.message,
        },
      ]);
    }
  };

  useEffect(() => {
    handleReadUsers();
  }, []);

  return (
    <UsersContext.Provider value={{ users, setUsers, handleReadUsers }}>
      {children}
    </UsersContext.Provider>
  );
}
