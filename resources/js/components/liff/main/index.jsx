import React, { useEffect, useRef, useState } from 'react'
import { Canvas } from '@react-three/fiber'
import Canvas3D from '../Components/Canvas3D'
import Content from '../Components/Content'

function App() {
  return (
    <main>
      <div className="canvas3D">
        <Canvas3D />
      </div>
      <Content />
    </main>
  )
}

export default App
