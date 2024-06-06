/* eslint-disable no-unused-vars */
import DataTable from "datatables.net-bs5";
import { useContext, useEffect, useRef } from "react";
import { Table } from "react-bootstrap";
import { UsersContext } from "../../../../context/site-administration/UsersContext";
import { useNavigate } from "react-router-dom";

const tableColumns = [
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
];

export default function UsersRead() {
  const navigate = useNavigate();
  const tableRef = useRef(null);
  const { users } = useContext(UsersContext);

  useEffect(() => {
    const dt = new DataTable(tableRef.current, {
      dom: `
        <"row mt-2 justify-content-between"
          <"col-sm-6 col-md-auto me-auto"l>
          <"col-sm-6 col-md-auto ms-auto"f>
        >
        <"row mt-2 justify-content-md-center"
          <"col-12"t>
        >
        <"row mt-2 justify-content-between"
          <"col-sm-6 col-md-auto me-auto"i>
          <"col-sm-6 col-md-auto ms-auto"p>
        >
        `,
      columns: tableColumns,
      data: users,
      responsive: true,
      createdRow: function (row, data, dataIndex) {
        row.setAttribute("role", "button");

        row.addEventListener("click", () => {
          navigate(`/site-administration/users/${data.idusers}`);
        });
      },
    });

    return () => {
      dt.destroy();
    };
  });

  return <Table hover ref={tableRef} />;
}
