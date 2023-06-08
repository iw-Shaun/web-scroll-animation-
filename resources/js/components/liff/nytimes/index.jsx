import React, { useEffect, useRef, useState } from 'react'
import { withStyles } from '@mui/styles';
import { gsap, Power1 } from 'gsap'
import { ScrollTrigger } from 'gsap/ScrollTrigger'
import useWindowDimensions from '../../window-dimensions';

const styles = (theme) => ({
  root: {
    display: 'flex',
    justifyContent: 'center',
    width: '100%',
    height: '200vh',
    '& #main': {  
      position: 'fixed',
      top: 0,
      zIndex: 1,
      width: '100%',
      maxWidth: '750px',
      '& .box': {
        display: 'flex',
        justifyContent: 'center',
        position: 'relative',
        overflow: 'hidden'
      },
    },
    '& .object': {
      position: 'absolute',
      top: 0,
    },
    '& #bg-1': {
      position: 'relative',
      zIndex: 0,
    },
    '& #people-1': {
      zIndex: 1,
    },
    '& #people-2': {
      zIndex: 2,
    },
    '& img': {
      width: '100%',
    },
  },
});

gsap.registerPlugin(ScrollTrigger)

function App(props) {
  const { classes } = props;
  const elementRef = useRef();
  const { width, height } = useWindowDimensions();
  const [ aspectRatio, setAspectRatio ] = useState(750)
  
  useEffect(()=>{
    const myDiv = document.getElementById("main");
    if (aspectRatio && width / height > aspectRatio && width <= height * aspectRatio) {
      myDiv.style.overflow = `visible`;
      myDiv.style.maxWidth = `${height * aspectRatio}px`;
    } else {
      myDiv.style.overflow = `hidden`;
    }
  },[width, height, aspectRatio])

  
  useEffect(()=>{

    ScrollTrigger.defaults({
      ease: Power1.easeInOut,
      immediateRender: false,
      scrub: 1,
      start: "top top",
      end: "50% top",
      // markers: true,
      toggleActions: "play pause resume reset",
    });

    gsap.to('#bg-1', 
      {
        scale: 1.2,
        y: '-=5%',
        scrollTrigger: {
          trigger: '#app',
        }
      }
    );

    gsap.to('#people-1', 
        {
          scale: 1.8,
          y: '-=15%',
          scrollTrigger: {
            trigger: '#app',
          }
        }
      );

      gsap.to('#people-2', 
        {
          scale: 2.5,
          y: '-=15%',
          scrollTrigger: {
            trigger: '#app',
          }
        }
      );
    
  },[elementRef])

  useEffect(()=>{
    setAspectRatio(0.95)
  },[])

  return (
    <div className={classes.root}>
      <div ref={elementRef} id='main' className='main'>
        <div className='box'>
          <img id='bg-1' className='object' src={window.assetUrl(`/images/nytimes/opener-bg_980.png`)}></img>
          <img id='people-1' className='object' src={window.assetUrl(`/images/nytimes/opener-1_980.png`)}></img>
          <img id='people-2' className='object' src={window.assetUrl(`/images/nytimes/opener-2_980.png`)}></img>
        </div>
      </div>
    </div>
  )
}

export default withStyles(styles)(App);
