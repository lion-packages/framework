import { Container, Table } from "react-bootstrap";
import { useUsers } from "../../../context/site-administration/UsersProvider";
import { useEffect, useRef } from "react";
import DataTable from "datatables.net-bs5";

export default function UsersIndex() {
  const { users } = useUsers();

  const tableRef = useRef(null);

  useEffect(() => {
    const dt = new DataTable(tableRef.current, {
      columns: [
        {
          data: "idusers",
          name: "#",
        },
        {
          data: "users_name",
          name: "NAME",
        },
        {
          data: "users_last_name",
          name: "LAST NAME",
        },
        {
          data: "users_nickname",
          name: "NICKNAME",
        },
        {
          data: "users_citizen_identification",
          name: "ID",
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
        <h3>Users</h3>

        <hr />

        <Table hover ref={tableRef} className="text-center">
          <thead>
            <tr>
              <th>#</th>
              <th>NAME</th>
              <th>LAST NAME</th>
              <th>NICKNAME</th>
              <th>ID</th>
            </tr>
          </thead>
          <tbody>
            {users.map((user, index) => (
              <tr key={index}>
                <td>{index + 1}</td>
                <td>{user.users_name}</td>
                <td>{user.users_last_name}</td>
                <td>{user.users_nickname}</td>
                <td>{user.users_citizen_identification}</td>
              </tr>
            ))}
          </tbody>
        </Table>
      </div>
    </Container>
  );
}
