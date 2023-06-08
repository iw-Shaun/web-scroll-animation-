import React, { useState, useEffect, useContext } from 'react';
import { Switch, Route, Link } from 'react-router-dom';
import PropTypes from 'prop-types';
import { withStyles } from '@mui/styles';
import liff from '@line/liff';
import queryString from 'query-string';
import { LiffProvider, LiffContext } from './liff-context';
import Animation from './main';
import Nytimes from './nytimes';
import Littlest from './LittlestTokyo';
import Firewatch from './firewatch';
import Test from './animate-test';
import trackingHandler from '../tracking-handler';
import useWindowDimensions from '../window-dimensions';
//import Util from '../util';

var parsedQueryString = {};

const styles = (theme) => ({
  root: {
  },
});

let prevPage = null; // For tracking.

function LiffRoot(props) {
  const { classes } = props;
  const { liffActions, liffState } = useContext(LiffContext);
  const { width, height } = useWindowDimensions();
  const [errorMsg, setErrorMsg] = useState('');
  
  const lineLogin = () => {
    console.log('lineLogin()');
    liff.init({ liffId: window.liffId })
      .then(() => {
        console.log('liff init');
        if (!liff.isLoggedIn()) {
          liff.login({
            redirectUri: window.location.href
          });
        } else {
          const accessToken = liff.getAccessToken();
          // const idToken = liff.getIDToken();
          liff.getProfile()
            .then(profile => {
              // Send the token and userId to server, then verify it.
              appLogin(profile.userId, accessToken)
                .then(getSelf);

              liff.getFriendship()
                .then((res) => {
                  if (res.friendFlag === true) {
                    console.log('getFriendship=true');
                  } else {
                    console.error('getFriendship=false');
                  }
                });
            })
            .catch((err) => {
              console.error('get profile failed:', err);
            });
        }
      })
      .catch((err) => {
        console.error(err);
      });
  }

  const appLogin = async (userId, accessToken) => {
    // Check if the login is performed.
    if (liffState.appLoggedIn) {
      return;
    }

    console.info('appLogin()')

    const data = {
      line_id: userId,
      access_token: accessToken,
      friendship: true
    }

    try {
      const res = await axios.post('/login', data);
      liffActions.setAppLoggedIn(true);
      trackingHandler.sendPageView(location);
    } catch(e) {
      setErrorMsg('請重新開啟頁面');
      console.error(e);
    }
  }
  
  const getSelf = () => {
    if (window.location.href.indexOf("login_invoice") !== -1) {
          console.log('url has login_invoice')
          axios.get('/message/push/invoice/register')
          .then(response => {
            // 然後關閉當前頁面
            console.log('register res', response)
            // liff.closeWindow();
            window.location.href = window.lineOaUrl;
          })
          .catch(error => {
            // 在這裡處理 axios post 的錯誤
          });
        }

    return axios.get('/me').then((res) => {
      let me = res.data.data;
      liffActions.setMe(me);
    }).catch((error) => {
      setErrorMsg('請重新開啟頁面');
      console.error('請重新開啟頁面');
    });
  }

  const trackingPageView = ({location}) => {
    const page = location.pathname;
    if (page != prevPage) { // Prevent duplicated request.
      document.getElementById("app").scrollTo(0, 0);
      if (liffState.appLoggedIn) {
        trackingHandler.sendPageView(location);
      }
      prevPage = location.pathname;
    }

    return null;
  }

  /**
   * Entry point.
   */
  useEffect(() => {
  }, []);

  return (
    <div className={`${classes.root}`}>
      {/* <div className="error-msg">{errorMsg}</div> */}
      <Switch>
        <Route path="/" exact component={Animation} />
        <Route path="/1" exact component={Nytimes} />
        <Route path="/2" exact component={Littlest} />
        <Route path="/3" exact component={Firewatch} />
        <Route path="/4" exact component={Test} />
      </Switch>
      <Route path="/" render={trackingPageView} />
    </div>
  )
}

LiffRoot.propTypes = {
  classes: PropTypes.object.isRequired,
};

function LiffRootWrap() {
  const A = withStyles(styles)(LiffRoot);
  return (
    <LiffProvider>
      <A />
    </LiffProvider>
  )
}

export default LiffRootWrap;
