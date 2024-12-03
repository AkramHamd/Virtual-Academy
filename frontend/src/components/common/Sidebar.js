import React from 'react';
import { Link } from 'react-router-dom';

const Sidebar = () => {
  return (
    <nav>
      <ul>
        <li>
          <Link to="/admin/comments">Manage Comments</Link>
        </li>
        {/* Otros enlaces */}
      </ul>
    </nav>
  );
};

export default Sidebar;
