import React, { useEffect, useRef } from 'react'
import { gsap } from 'gsap'
import { ScrollTrigger } from 'gsap/ScrollTrigger'

gsap.registerPlugin(ScrollTrigger)

function Content() {
  const ref = useRef()

  useEffect(()=>{
    const sections = ["#section-1", "#section-2", "#section-3", "#section-4", "#section-5", "#section-6"];

    sections.forEach(section => {
      gsap.fromTo(section, 
        { opacity: 0 }, 
        {
          opacity: 1,
          scrollTrigger: {
            trigger: section,
            scrub: true,
            start: "top 90%",
            end: "bottom 60%",
          }
        }
      );
    });
    
  },[ref])

  return (
    <div ref={ref} className="content">
      <section className="section-one">
        <h1 id='section-1'>SECTION 1</h1>
      </section>
      <section className="section-two">
        <h1 id='section-2'>SECTION 2</h1>
      </section>
      <section className="section-three">
        <h1 id='section-3'>SECTION 3</h1>
      </section>
      <section className="section-four">
        <h1 id='section-4'>SECTION 4</h1>
      </section>
      <section className="section-five">
        <h1 id='section-5'>SECTION 5</h1>
      </section>
      <section className="section-six">
        <h1 id='section-6'>SECTION 6</h1>
      </section>
      <section className="footer">
        <h1 id='footer'></h1>
      </section>
    </div>
  )
}

export default Content
