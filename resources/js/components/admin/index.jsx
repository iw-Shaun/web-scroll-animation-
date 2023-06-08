import React, { useEffect, useContext, useState } from 'react';
import { Switch, Route, Link, useHistory } from 'react-router-dom';
import PropTypes from 'prop-types';
import { withStyles } from '@mui/styles';
import { Box } from '@mui/material';
import { AdminProvider, AdminContext } from './admin-context';
import './admin.scss'; // Override the app.scss.
import axios from 'axios';

const styles = (theme) => ({
  root: {
    color: '#444444',
    '&> .menu': {
      display: 'flex',
      marginBottom: '10px',
      padding: '5px',
      background: '#F7F1EA',
      '& .logo': {
        width: '160px',
        height: '50px',
        background: `center / contain no-repeat url(${window.assetUrl('/images/theme-exhibition/common/logo.png')})`,
      },
      '& .item': {
        display: 'flex',
        alignItems: 'center',
        justifyContent: 'center',
        margin: '5px 16px',
        cursor: 'pointer',
        '&.right': {
          marginLeft: 'auto',
        },
        '& a': {
          color: '#606266',
          textDecoration: 'none',
        },
      },
    },
  }
});

function AdminRoot(props) {
  return <div style={{overflow:'hidden'}}>請選擇上方功能</div>
}

function Admin(props) {
  const { classes } = props;
  const { adminActions, adminState } = useContext(AdminContext);

  useEffect(() => {
    if (adminState.me === null) {
      adminActions.getAdminMe();
    }
  }, []);

  const logout = () => {
    const data = {
      logout: 1
    }
    axios.post('/admin/logout', data)
      .then(() => {
        window.location.href = '/admin/login';
      })
      .catch((err) => {
        alert('無法登出');
      });
  }
  return (
    <div className={classes.root}>
      {adminState.me === null ?
        <div>Loading...</div>
        :
        <>
          <Box className="menu">
            <div className="logo fctr">DEVELOPER 後台</div>
            <div className="item"><Link to='/admin/null'>NULL</Link></div>
            <div className="item right">
              <div onClick={logout}>登出</div>
            </div>
          </Box>

          <Switch>
            <Route path='/admin' exact component={AdminRoot}/>
          </Switch>
        </>
      }
    </div>
  )
}

Admin.propTypes = {
  classes: PropTypes.object.isRequired,
};

function AdminWrap() {
  const A = withStyles(styles)(Admin);
  return (
    <AdminProvider>
      <A />
    </AdminProvider>
  )
}

export default AdminWrap;
