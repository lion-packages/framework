import { Button, Container, Table } from "react-bootstrap";
import { useUsers } from "../../../context/site-administration/UsersProvider";
import { useEffect, useRef, useState } from "react";
import DataTable from "datatables.net-bs5";
import UsersCreate from "./components/UsersCreate";

export default function UsersIndex() {
  const { users, handleReadUsers } = useUsers();

  const tableRef = useRef(null);

  const [show, setShow] = useState(false);

  useEffect(() => {
    const dt = new DataTable(tableRef.current, {
      dom: `
        <"row mt-2 justify-content-between"
          <"col-md-auto me-auto"l>
          <"col-md-auto ms-auto"f>
        >
        <"row mt-2 justify-content-md-center"
          <"col-12"t>
        >
        <"row mt-2 justify-content-between"
          <"col-md-auto me-auto"i>
          <"col-md-auto ms-auto"p>
        >
        `,
      columns: [
        {
          data: "users_name",
          title: "NAME",
        },
        {
          data: "users_last_name",
          title: "LAST NAME",
        },
        {
          data: "users_nickname",
          title: "NICKNAME",
        },
        {
          data: "users_citizen_identification",
          title: "ID",
        },
      ],
      data: users,
    });

    return () => {
      dt.destroy();
    };
  });

  return (
    <Container>
      <div className="my-3">
        <div className="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
          <h4>Users</h4>

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
        </div>

        <UsersCreate show={show} setShow={setShow} />

        <Table hover ref={tableRef} />
      </div>
    </Container>
  );
}
