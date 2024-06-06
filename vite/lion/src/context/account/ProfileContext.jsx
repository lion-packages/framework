/* eslint-disable react/prop-types */
import axios from "axios";
import { createContext, useContext, useState } from "react";
import useApiResponse from "../../hooks/useApiResponse";
import { AuthContext } from "../AuthContext";
import { ResponseContext } from "../ResponseContext";
import axiosApi from "../../Api";

export const ProfileContext = createContext();

export function ProfileProvider({ children }) {
  const { refreshToken } = useContext(AuthContext);
  const { addToast } = useContext(ResponseContext);
  const { getResponseFromRules } = useApiResponse();

  const [idroles, setIdroles] = useState("");
  const [users_email, setUsers_email] = useState("");
  const [iddocument_types, setIddocument_types] = useState("");
  const [users_citizen_identification, setUsers_citizen_identification] =
    useState("");
  const [users_name, setUsers_name] = useState("");
  const [users_last_name, setUsers_last_name] = useState("");
  const [users_nickname, setUsers_nickname] = useState("");

  const handleReadProfile = async () => {
    const res = await axiosApi(refreshToken).get(`/api/profile`);

    if (res.data) {
      setIdroles(res.data.idroles);

      setIddocument_types(res.data.iddocument_types || "");

      setUsers_citizen_identification(
        res.data.users_citizen_identification || ""
      );

      setUsers_name(res.data.users_name || "");

      setUsers_last_name(res.data.users_last_name || "");

      setUsers_nickname(res.data.users_nickname || "");

      setUsers_email(res.data.users_email || "");
    }
  };

  const handleUpdateProfile = async (event) => {
    event.preventDefault();

    const form = {
      iddocument_types: parseInt(iddocument_types),
      users_citizen_identification: users_citizen_identification,
      users_name: users_name,
      users_last_name: users_last_name,
      users_nickname: users_nickname,
    };

    const res = await axiosApi(refreshToken).put(`/api/profile`, form);

    if (res.data) {
      addToast([
        {
          status: res.data.status,
          title: "Profile",
          message: res.data.message,
        },
      ]);
    }

    if (res.response) {
      if (500 === res.response.data.code) {
        addToast([...getResponseFromRules("Profile", res.response.data)]);
      }
    }
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
