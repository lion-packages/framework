/* eslint-disable react/prop-types */
import { createContext, useContext, useEffect, useState } from "react";
import axios from "axios";
import { AuthContext } from "../AuthContext";
import { ResponseContext } from "../ResponseContext";

export const UsersContext = createContext();

export function UsersProvider({ children }) {
  const { getJWT } = useContext(AuthContext);
  const { addToast } = useContext(ResponseContext);

  const [users, setUsers] = useState([]);

  const handleReadUsers = () => {
    axios
      .get(`${import.meta.env.VITE_SERVER_URL_AUD}/api/users`, {
        headers: {
          Authorization: `Bearer ${getJWT()}`,
        },
      })
      .then(({ data }) => {
        if (!data.status) {
          setUsers(data);

          addToast([
            {
              status: "success",
              title: "Users",
              message: "Data updated correctly",
            },
          ]);
        }
      })
      .catch(({ response }) => {
        // console.log(response.data);

        if (403 === response.data.code) {
          addToast([
            {
              status: response.data.status,
              title: "Users",
              message: response.data.message,
            },
          ]);
        }
      });
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
