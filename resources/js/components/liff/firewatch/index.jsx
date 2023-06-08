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
      position: 'relative',
      zIndex: 1,
      width: '100%',
      maxWidth: '750px',
      background: '#210002',
      '& .box': {
        display: 'flex',
        justifyContent: 'center',
        position: 'relative',
        overflow: 'hidden',
        width: '100%',
        height: '100vw',
        maxHeight: '750px',
        background: '#ffaf1b',
        marginBottom: '100%',
      },
      '& h1': {
        textAlign: 'center',
        color: 'white',
      },
    },
    '& .object': {
      position: 'absolute',
      width: '250%',
      bottom: 0,
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
    
    const sections = [100, 70 ,70 ,60 ,55 ,35 ,30, 30];

    sections.forEach((section, index) => {
      gsap.to(`#bg-${index}`, 
        {
          y: `+=${section}%`,
          scrollTrigger: {
            trigger: '#app',
          }
        }
      );
    });
    
  },[elementRef])

  useEffect(()=>{
  },[])

  return (
    <div className={classes.root}>
      <div ref={elementRef} id='main' className='main'>
        <div className='box'>
          <img id='bg-0' className='object' src={window.assetUrl(`/images/firewatch/parallax0.png`)}></img>
          <img id='bg-1' className='object' src={window.assetUrl(`/images/firewatch/parallax1.png`)}></img>
          <img id='bg-2' className='object' src={window.assetUrl(`/images/firewatch/parallax2.png`)}></img>
          <img id='bg-3' className='object' src={window.assetUrl(`/images/firewatch/parallax3.png`)}></img>
          <img id='bg-4' className='object' src={window.assetUrl(`/images/firewatch/parallax4.png`)}></img>
          <img id='bg-5' className='object' src={window.assetUrl(`/images/firewatch/parallax5.png`)}></img>
          <img id='bg-6' className='object' src={window.assetUrl(`/images/firewatch/parallax6.png`)}></img>
          <img id='bg-7' className='object' src={window.assetUrl(`/images/firewatch/parallax7.png`)}></img>
          <img id='bg-8' className='object' src={window.assetUrl(`/images/firewatch/parallax8.png`)}></img>
        </div>
        <h1>TEST PAGE</h1>
      </div>
    </div>
  )
}

export default withStyles(styles)(App);
