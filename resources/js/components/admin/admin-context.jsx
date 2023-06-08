import React, { useReducer } from 'react';
import axios from 'axios';
import queryString from 'query-string';
import AppError from '../app-error';

const GET_ADMIN_ME = 'GET_ADMIN_ME';

const reducer = (state, action) => {
  switch (action.type) {
    case GET_ADMIN_ME:
      return {
        ...state,
        me: action.payload,
      };
    default:
      throw new Error();
  }
};

const AdminContext = React.createContext();
const initialState = {
  me: null,
};

const AdminProvider = (props) => {
  const [state, dispatch] = useReducer(reducer, initialState);

  const actions = {
    getAdminMe: async () => {
      try {
        const res = await axios.get('/admin/me');
        dispatch({ type: GET_ADMIN_ME, payload: res.data.data });
      } catch (error) {
        const code = error.response?.data.code;
        switch (code) {
          case AppError.ACCOUNT_IS_INACTIVE:
            const query = queryString.stringify({
              message: '該帳號未啟用',
            });
            window.location.href = `/admin/login-fail?${query}`;
            break;
          default:
            dispatch({ type: GET_ADMIN_ME, payload: null });
            // Redirect to login page.
            window.location.href = '/admin/login';
            break;
        }
      };
    },
  };

  const { children } = props;
  return (
    <AdminContext.Provider
      value={{
        adminState: state,
        adminActions: actions,
      }}
    >
      {children}
    </AdminContext.Provider>
  );
};

export { AdminProvider, AdminContext };
