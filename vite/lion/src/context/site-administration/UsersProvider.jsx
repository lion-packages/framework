import { createContext, useContext, useEffect, useState } from "react";
import { useAuth } from "../AuthProvider";
import axios from "axios";
import { useResponse } from "../ResponseProvider";

const UsersContext = createContext();

export function UsersProvider({ children }) {
  const { getJWT } = useAuth();
  const { addToast } = useResponse();

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

export function useUsers() {
  return useContext(UsersContext);
}
