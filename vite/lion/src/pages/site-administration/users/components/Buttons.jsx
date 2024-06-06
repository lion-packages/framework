/* eslint-disable react/prop-types */
import { useContext } from "react";
import { Button } from "react-bootstrap";
import { UsersContext } from "../../../../context/site-administration/UsersContext";

export default function Buttons({ setShow }) {
  const { handleReadUsers } = useContext(UsersContext);

  return (
    <div className="btn-toolbar mb-2 mb-md-0">
      <Button
        type="button"
        className="btn btn-sm btn-primary me-2"
        onClick={() => handleReadUsers()}
      >
        Reload
      </Button>

      <Button
        type="button"
        className="btn btn-sm btn-primary"
        onClick={() => setShow(true)}
      >
        Add
      </Button>
    </div>
  );
}
