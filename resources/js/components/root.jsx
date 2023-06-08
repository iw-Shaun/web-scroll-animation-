import React from 'react';
import { createRoot } from 'react-dom/client';
import ReactDOM from 'react-dom';
import { BrowserRouter, Switch, Route } from 'react-router-dom';
import Modal from 'react-modal';
import Liff from './liff/index';
import Admin from './admin/index';
import Login from './admin/login';
import ChangePassword from './admin/change-password';

Modal.setAppElement('#my-modal')

function Root() {
  return (
    <BrowserRouter>
      <Switch>
        <Route path="/admin/login" component={Login} />
        <Route path="/admin/change_password" component={ChangePassword} />
        <Route path='/admin' component={Admin} />
        <Route path='/' component={Liff} />
      </Switch>
    </BrowserRouter>
  );
}

export default Root;

createRoot(document.getElementById('app')).render(
  <React.StrictMode>
    <Root />
  </React.StrictMode>
);
// ReactDOM.render(
//   <React.StrictMode>
//     <Root />
//   </React.StrictMode>,
//   document.getElementById('app')
// );