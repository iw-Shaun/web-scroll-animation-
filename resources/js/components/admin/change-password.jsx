import * as React from 'react';
import { useState, useEffect } from 'react';
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
  const [oldPassword, setOldPassword] = useState('');
  const [newPassword, setNewPassword] = useState('');
  const [confirmPassword, setConfirmPassword] = useState('');
  const [errorMessage, setErrorMessage] = useState('');

  const confirm = async () => {
    try {
      const params = {
        old_password: oldPassword,
        new_password: newPassword,
      }
      const res = await axios.post('/admin/change_password', params);
      // Back to admin page.
      alert('密碼修改完成，下次登入請使用新密碼');
      window.location.href = '/admin';
    } catch(err) {
      const code = err.response.data.code;
      switch (code) {
        case AppError.INVALID_PASSWORD:
          setErrorMessage('舊密碼錯誤');
          break;
        case AppError.PASSWORD_FORMAT_ERROR:
          setErrorMessage('密碼格式有誤');
          break;
        default:
          setErrorMessage('無法變更密碼，請稍後重試');
          break;
      }
    }
  }

  useEffect(() => {
    let msg = '';
    if (oldPassword && newPassword) {
      if (oldPassword == newPassword) {
        msg = '新舊密碼不得相同';
      }
    }
    if (newPassword && confirmPassword) {
      if (newPassword != confirmPassword) {
        msg = '新密碼兩次輸入不同';
      } else if (newPassword.length < 10) {
        msg = '新密碼至少10位數';
      }
    }
    if (msg) {
      setErrorMessage(msg);
    } else {
      setErrorMessage('');
    }
  }, [oldPassword, newPassword, confirmPassword]);

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
          <Typography component="h1" variant="h5">更改密碼</Typography>
          <Box component="form" noValidate sx={{ mt: 1 }}>
            <TextField margin="normal" required fullWidth name="old_password" label="舊密碼" type="password" id="password"
              value={oldPassword} onChange={(e) => setOldPassword(e.target.value)} />
            <TextField margin="normal" required fullWidth name="new_password" label="新密碼 (至少10個字，需要包含英文字母與符號)" type="password" id="password"
              value={newPassword} onChange={(e) => setNewPassword(e.target.value)} />
            <TextField margin="normal" required fullWidth name="confirm_password" label="確認新密碼" type="password" id="password"
              value={confirmPassword} onChange={(e) => setConfirmPassword(e.target.value)} />
            <div style={{ color: 'red' }}>{errorMessage}</div>
            <Button type="button" fullWidth variant="contained" sx={{ mt: 3, mb: 2 }} onClick={confirm} disabled={!oldPassword || !newPassword || !confirmPassword}>確認變更</Button>
          </Box>
        </Box>
      </Container>
    </ThemeProvider>
  );
}
