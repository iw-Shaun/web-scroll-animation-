import React, { useState, useEffect } from 'react';
import Avatar from '@mui/material/Avatar';
import Button from '@mui/material/Button';
import CssBaseline from '@mui/material/CssBaseline';
import TextField from '@mui/material/TextField';
import Box from '@mui/material/Box';
import LockOutlinedIcon from '@mui/icons-material/LockOutlined';
import Typography from '@mui/material/Typography';
import Container from '@mui/material/Container';
import { createTheme, ThemeProvider } from '@mui/material/styles';
import axios from 'axios';
import AppError from '../app-error';

const theme = createTheme();

export default function SignIn() {
  const [email, setEmail] = useState('');
  const [password, setPassword] = useState('');

  const login = async () => {
    const data = {
      email: email,
      password: password,
    }
    try {
      const res = await axios.post('/admin/login', data);
      window.location.href = '/admin';
    } catch(err) {
      if (err.response.data.code == AppError.INVALID_USER_OR_PASSWORD) {
        alert('帳號或密碼有誤');
      } else if (err.response.data.code == AppError.INVALID_SOURCE_ID) {
        alert('您所在的網路環境不允許登入系統');
      } else if (err.response.data.code == AppError.ACCOUNT_IS_INACTIVE) {
        alert('您的帳號因長時間未登入而被鎖住，請聯絡管理員');
      } else if (err.response.data.code == AppError.LOGIN_FAILED_COUNT_LIMIT) {
        alert('登入錯誤次數過多，請稍後重試')
      } else {
        alert('登入失敗');
      }
    };
  }

  return (
    <ThemeProvider theme={theme}>
      <Container component="main" maxWidth="xs">
        <CssBaseline />
        <Box
          sx={{
            marginTop: 8,
            display: 'flex',
            flexDirection: 'column',
            alignItems: 'center',
          }}
        >
          <Avatar sx={{ m: 1, bgcolor: 'secondary.main' }}>
            <LockOutlinedIcon />
          </Avatar>
          <Typography component="h1" variant="h5">登入</Typography>
          {window.enPwdLogin == 1 ?
            <Box component="form" noValidate sx={{ mt: 1 }}>
              <TextField margin="normal" required fullWidth id="email" label="Email" name="email" autoComplete="email" autoFocus
                value={email} onChange={(e) => setEmail(e.target.value)} />
              <TextField margin="normal" required fullWidth name="password" label="密碼" type="password" id="password" autoComplete="current-password"
                value={password} onChange={(e) => setPassword(e.target.value)} />
              <Button type="button" fullWidth variant="contained" sx={{ mt: 3, mb: 2 }} onClick={login} disabled={!email || !password}>登入</Button>
            </Box>
            : null}
        </Box>
      </Container>
    </ThemeProvider>
  );
}
