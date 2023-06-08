import React, { useRef, useEffect, useState } from 'react';
import { Canvas, useFrame, useThree } from '@react-three/fiber';
import { Image as ImageImpl , ScrollControls, useScroll, OrbitControls } from '@react-three/drei';
import * as THREE from 'three';
import { ScrollTrigger } from 'gsap/ScrollTrigger'
import { gsap } from 'gsap';

gsap.registerPlugin(ScrollTrigger);
const CameraSwitcher = () => {
  const { camera } = useThree();

	var sectionDuration = 1;
  let tl = new gsap.timeline(
    {
      scrollTrigger: {
        trigger: "#test",
        scrub: true,
        start: "top top",
        end: "bottom bottom",
        markers: true,
      },
      defaults: {duration: sectionDuration, ease: 'power2.inOut'}
    });
    
	let delay = 0;
	tl.to(camera.position, {z: 100, ease: 'power2.inOut'}, delay);

  useFrame(() => {
    // const { x, y, z } = camera.position;
    // const m = scroll.offset * 10;
    // const index = Math.floor(m);
    // const offset = [
    //   [0, 0, 100 - m * 10],
    //   [0, 0, 100 - m * 10],
    //   [0, (m - index) * -3, 100 - m * 10],
    //   [0, -3, 70],
    //   [-(m - index) * 1.68, -3 + (m - index) * -6.3, 70 - (m - index) / 2],
    //   [-1.68, -9.3, 69.5],
    //   [-1.68, -9.3, 69.5],
    //   [-1.68, -9.3, 69.5],
    //   [-1.68, -9.3, 69.5],
    //   [-1.68, -9.3, 69.5],
    //   [-1.68, -9.3, 69.5],
    //   [-1.68, -9.3, 69.5],
    // ];

    // const look_at = [
    //   [0, 0, 0],
    //   [(m - index) / 2, 0, 0],
    //   [0.5 + (m - index) / 2, 0, 0],
    //   [1, 0, 0],
    //   [1, 0, 0],
    //   [1, 0, 0],
    //   [1, 0, 0],
    //   [1, 0, 0],
    //   [1, 0, 0],
    //   [1, 0, 0],
    //   [1, 0, 0],
    // ];

    // const easing = 0.05; // 緩動值

    // const targetPosition = offset[index] || [0, 0, 0];
    // const targetLookAt = look_at[index] || [0, 0, 0];

    // camera.position.lerp(new THREE.Vector3(...targetPosition), easing);
    // camera.quaternion.setFromAxisAngle(new THREE.Vector3(...targetLookAt), -Math.PI / 2);
  });

  return null;
};

function Image({ c = new THREE.Color(), ...props }) {
  const ref = useRef();
  const pos = props.position[2];
  const { camera } = useThree();

  useFrame(() => {
    if (props.hide) {
      const cameraPosition = camera.position.z;
      const distance = Math.abs(cameraPosition - pos);
      const maxDistance = 20; // 最大視野
      var opacity = 0;
  
      if (distance <= maxDistance) {
        const aa = Math.abs((maxDistance-distance)/10)
        opacity = Math.min(aa, 1);
      }
  
      ref.current.material.opacity = opacity;
    }
  });

  return (
    <ImageImpl
      raycast={() => false}
      ref={ref}
      alphaTest={0.1}
      transparent
      toneMapped={false}
      {...props}
    />
  );
}

const Images = ({ images, position: pos, rotation: rot, hide }) => {
  const [loadedTextures, setLoadedTextures] = useState([]);
  const group = useRef()

  useEffect(() => {
    const textureLoader = new THREE.TextureLoader();

    const loadTextures = async () => {
      const loadedImages = await Promise.all(images.map((image) =>{
        const url = window.assetUrl(`/images/misatoto/${image.img}`)
        return textureLoader.loadAsync(url)
      }));
      setLoadedTextures(loadedImages);
    };

    loadTextures();
  }, [images]);

  return (
    <group ref={group} position={pos} rotation={rot}>
      {loadedTextures.map((texture, index) => {
        const image = texture.image;
        const { position, rotation, img, size } = images[index];
        const aspectRatio = image.width / image.height;
        const planeHeight = size || 5;
        const planeWidth = planeHeight * aspectRatio;

        return (
          <Image
            key={index}
            position={position}
            rotation={rotation}
            scale={[planeWidth, planeHeight, 1]}
            hide={hide}
            url={window.assetUrl(`/images/misatoto/${img}`)}
          />
        );
      })}
    </group>
  );
};

export default function App() {
  const images = [
    { img: 'cloud_1.png', position: [-20, 10, 89], rotation:[0, 0, 0], size:10},
    { img: 'cloud_2.png', position: [-25, 0, 88], rotation:[0, 0, 0], size:10},
    { img: 'cloud_large_left.png', position: [-10, -6, 86], rotation:[0, 0, 0], size:10},
    { img: 'cloud_large_right.png', position: [10, 8, 85], rotation:[0, 0, 0], size:10},
    { img: 'cloud_light_5.png', position: [25, -5, 83], rotation:[0, 0, 0], size:10},
    { img: 'cloud_1.png', position: [6, -5, 80], rotation:[0, 0, 0], size:10},
    { img: 'cloud_light_1.png', position: [-10, 3, 70], rotation:[0, 0, 0], size:10},
    { img: 'cloud_1.png', position: [6, -3, 40], rotation:[0, 0, 0], size:10},
  ];
  
  const images2 = [
    { img: 'map_20230403.png', position: [0, 0, 0], rotation:[0, 0, 0], size:10},
    { img: 'cloud_light_5.png', position: [-3, -1.5, 3.5], rotation:[0, 0, 0], size:1},
    { img: 'cloud_light_2.png', position: [3, 2, 3.5], rotation:[0, 0, 0], size:1},
    { img: 'cloud_1.png', position: [3, -1.5, 5], rotation:[0, 0, 0], size:1},
    { img: 'cloud_light_2.png', position: [-3, 2, 5], rotation:[0, 0, 0], size:1},
    { img: 'cloud_2.png', position: [-8, 0, 5], rotation:[(Math.PI/4), 0, 0], size:3},
    { img: 'cloud_light_5.png', position: [3, -5, 5], rotation:[(Math.PI/4), 0, 0], size:3},
    { img: 'cloud_1.png', position: [-3, -3.5, 5], rotation:[0, 0, 0], size:1},
    { img: 'cloud_1.png', position: [3, 2, 4], rotation:[0, 0, 0], size:0.5},
  ];

  return (
    <Canvas id="test" style={{ position: 'fixed', top: 0, left: 0, height: '100vh', background: '#efdbcb' }}>
      <Images position={[0, 0, 0]} rotation={[0, 0, 0]} images={images} hide={true}/>
      <Images position={[0, -10, 70]} rotation={[-(Math.PI/2), 0, 0]} images={images2} roll={true}/>
      <CameraSwitcher />
      {/* <OrbitControls /> */}
    </Canvas>
  );
}
