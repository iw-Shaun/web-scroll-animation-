import queryString from 'query-string';

const trackingHandler = {
  sendPageView: (location) => {
    // Store in our server after login.

    const params = {
      action: 'page_view',
      category: location.pathname,
    }

    gtag('event', 'liff_page_view', params);
    
    axios.get('/tracking_event', { params: params });
  },
  trackingEvent: (action, category) => {
    const params = {
      action: action,
      category: category,
    }

    // GA event
    gtag('event', action, params);

    // Backend
    axios.get('/tracking_event', { params: params });
  }
}

export default trackingHandler;
