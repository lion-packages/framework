import axios from "axios";
import { createContext, useContext, useState } from "react";
import { useAuth } from "../AuthProvider";
import { useResponse } from "../ResponseProvider";
import useApiResponse from "../../hooks/useApiResponse";

const ProfileContext = createContext();

export function ProfileProvider({ children }) {
  const { getJWT } = useAuth();
  const { addToast } = useResponse();
  const { getResponseFromRules } = useApiResponse();

  const [idroles, setIdroles] = useState("");
  const [users_email, setUsers_email] = useState("");
  const [iddocument_types, setIddocument_types] = useState("");
  const [users_citizen_identification, setUsers_citizen_identification] =
    useState("");
  const [users_name, setUsers_name] = useState("");
  const [users_last_name, setUsers_last_name] = useState("");
  const [users_nickname, setUsers_nickname] = useState("");

  const handleReadProfile = () => {
    axios
      .get(`${import.meta.env.VITE_SERVER_URL_AUD}/api/profile`, {
        headers: {
          Authorization: `Bearer ${getJWT()}`,
        },
      })
      .then(({ data }) => {
        setIdroles(data.idroles);
        setIddocument_types(data.iddocument_types);
        setUsers_citizen_identification(data.users_citizen_identification);
        setUsers_name(data.users_name);
        setUsers_last_name(data.users_last_name);
        setUsers_nickname(data.users_nickname);
        setUsers_email(data.users_email);
      })
      .catch((err) => {
        console.log(err);
      });
  };

  const handleUpdateProfile = (event) => {
    event.preventDefault();

    const form = {
      iddocument_types: parseInt(iddocument_types),
      users_citizen_identification: users_citizen_identification,
      users_name: users_name,
      users_last_name: users_last_name,
      users_nickname: users_nickname,
    };

    axios
      .put(`${import.meta.env.VITE_SERVER_URL_AUD}/api/profile`, form, {
        headers: {
          Authorization: `Bearer ${getJWT()}`,
        },
      })
      .then(({ data }) => {
        addToast([
          {
            status: data.status,
            title: "Profile",
            message: data.message,
          },
        ]);
      })
      .catch(({ response }) => {
        if (500 === response.data.code) {
          addToast([...getResponseFromRules("Profile", response.data)]);
        }
      });
  };

  return (
    <ProfileContext.Provider
      value={{
        idroles,
        iddocument_types,
        users_citizen_identification,
        users_name,
        users_last_name,
        users_nickname,
        users_email,
        setIdroles,
        setIddocument_types,
        setUsers_citizen_identification,
        setUsers_name,
        setUsers_last_name,
        setUsers_nickname,
        setUsers_email,
        handleReadProfile,
        handleUpdateProfile,
      }}
    >
      {children}
    </ProfileContext.Provider>
  );
}

export function useProfile() {
  return useContext(ProfileContext);
}
