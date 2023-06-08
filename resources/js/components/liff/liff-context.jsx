import React, { useReducer } from 'react';
import axios from 'axios';

const SET_APP_LOGGED_IN = 'SET_APP_LOGGED_IN';
const SET_ME = 'SET_ME';
const SET_SCORE = 'SET_SCORE';
const SET_ASPECTRATIO = 'SET_ASPECTRATIO';

const reducer = (state, action) => {
  switch (action.type) {
    case SET_APP_LOGGED_IN:
      return {
        ...state,
        appLoggedIn: action.payload,
      };
    case SET_ME:
      return {
        ...state,
        me: action.payload,
      };
    case SET_SCORE:
      return {
        ...state,
        score: action.payload,
      };
    case SET_ASPECTRATIO:
      return {
        ...state,
        aspectRatio: action.payload,
      };
    default:
      throw new Error();
  }
};

const LiffContext = React.createContext();
const initialState = {
  aspectRatio: 0.55,
  appLoggedIn: false,
  me: null,
  score: [],
};

const LiffProvider = (props) => {
  const [state, dispatch] = useReducer(reducer, initialState);

  const actions = {
    setAppLoggedIn: (flag) => {
      dispatch({ type: SET_APP_LOGGED_IN, payload: flag });
    },
    setMe: (me) => {
      dispatch({ type: SET_ME, payload: me });
    },
    setScore: (score) => {
      dispatch({ type: SET_SCORE, payload: score });
    },
    setAspectRatio: (aspectRatio) => {
      dispatch({ type: SET_ASPECTRATIO, payload: aspectRatio });
    }
  };

  const { children } = props;
  return (
    <LiffContext.Provider
      value={{
        liffState: state,
        liffActions: actions,
      }}
    >
      {children}
    </LiffContext.Provider>
  );
};

export { LiffProvider, LiffContext };
