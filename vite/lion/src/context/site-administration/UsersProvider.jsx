import { createContext, useContext, useEffect, useState } from "react";
import { useAuth } from "../AuthProvider";
import axios from "axios";

const UsersContext = createContext();

export function UsersProvider({ children }) {
  const { getJWT } = useAuth();

  const [users, setUsers] = useState([]);

  const handleReadUsers = () => {
    axios
      .get(`${import.meta.env.VITE_SERVER_URL_AUD}/api/users`, {
        headers: {
          Authorization: `Bearer ${getJWT()}`,
        },
      })
      .then(({ data }) => {
        setUsers(data.status ? [] : data);
      })
      .catch(({ response }) => {
        console.log(response.data);
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
