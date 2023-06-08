import React, { useRef, useEffect, useState } from 'react';
import { Canvas, useFrame, useThree } from '@react-three/fiber';
import { Image as ImageImpl, OrbitControls } from '@react-three/drei';
import * as THREE from 'three';
import { gsap, Power1 } from 'gsap';
import { ScrollTrigger } from 'gsap/ScrollTrigger';

gsap.registerPlugin(ScrollTrigger);

const CameraController = () => {
  const { camera } = useThree();

  ScrollTrigger.defaults({
    immediateRender: false,
    scrub: 1,
    ease: Power1.easeInOut,
    toggleActions: "play pause resume reset",
  });

  useEffect(() => {
    camera.position.set(0, 0, 100);
  }, []);

  useEffect(() => {
    const tl1 = gsap.timeline({
      scrollTrigger: {
        trigger: '.section-one',
        start: 'top top',
        endTrigger: '.section-three',
        end: 'bottom bottom',
      },
    })
    .to(camera.position, {
      x:0,
      y:0,
      z: 70,
    })
    .to(camera.rotation, {
      x: -Math.PI/2,
      y:0,
      z:0,
    })
    .to(camera.position, {
      x: 0.5,
      y: -7.5,
      z: 72.5,
    });

    const tl2 = gsap.timeline({
      scrollTrigger: {
        trigger: '.section-four',
        start: 'top top',
        end: 'bottom bottom',
      },
    }).to(camera.position, {
      x: 1.5,
      y: -7.5,
      z: 70,
    });

    const tl3 = gsap.timeline({
      scrollTrigger: {
        trigger: '.section-five',
        start: 'top top',
        end: 'bottom bottom',
      },
    }).to(camera.position, {
      x: -1,
      y: -7.5,
      z: 69,
    });


    const curve = new THREE.CatmullRomCurve3([
      new THREE.Vector3(-1, -7.5, 69),
      new THREE.Vector3(-1.6, -9.8, 68.8),
      new THREE.Vector3(-1.6, -11, 68.8),
      new THREE.Vector3(0.1, -12, 68.8),
    ]);

    const tl4 = gsap.timeline({
      scrollTrigger: {
        trigger: '.section-six',
        start: 'top top',
        end: 'bottom bottom',
      },
    })
    .to(camera.position, {
      t: 1, // 將 t 值設為 1，表示移動到曲線的結束點
      duration: 5,
      onUpdate: () => {
        const position = curve.getPointAt(camera.position.t); // 獲取曲線上指定 t 值的位置
        camera.position.copy(position); // 更新相機位置
      }
    })

  }, [camera]);

  return null;
};

function Image({ ...props }) {
  const ref = useRef();
  const pos = props.position[2];
  const { camera } = useThree();

  useFrame(() => {
    if (props.hide) {
      const cameraPosition = camera.position.z;
      const distance = Math.abs(cameraPosition - pos);
      const maxDistance = 25; // 最大视野
      var opacity = 0;

      if (distance <= maxDistance) {
        const aa = Math.abs((maxDistance - distance) / 10);
        opacity = Math.min(aa, 1);
      }

      ref.current.material.opacity = opacity;
    }
  });

  return (
    <ImageImpl
      ref={ref}
      raycast={() => false}
      transparent={true}
      toneMapped={false}
      {...props}
    />
  );
}

function textureLoader(images, setLoadedTextures) {
  const textureLoader = new THREE.TextureLoader();

  const loadTextures = async () => {
    const loadedImages = await Promise.all(images.map((image) => {
      const url = window.assetUrl(`/images/misatoto/${image.img}.png`);
      return textureLoader.loadAsync(url);
    }));
    setLoadedTextures(loadedImages);
  };

  loadTextures();
}

const ImageGroup = ({ images, pos, rot, hide }) => {
  const [loadedTextures, setLoadedTextures] = useState([]);
  const group = useRef()

  useEffect(() => {
    const textureLoader = new THREE.TextureLoader();

    const loadTextures = async () => {
      const loadedImages = await Promise.all(images.map((image) => {
        const url = window.assetUrl(`/images/misatoto/${image.img}.png`);
        return textureLoader.loadAsync(url);
      }));
      setLoadedTextures(loadedImages);
    };

    loadTextures();
  }, [images]);

  return (
    <group ref={group} position={pos} rotation={rot}>
      {loadedTextures.map((texture, index) => {
        const image = texture.image;
        const { position, rotation, img, size, order } = images[index];
        const aspectRatio = image.width / image.height;
        const planeHeight = size || 5;
        const planeWidth = planeHeight * aspectRatio;
        return (
          <Image
            key={index}
            name={img}
            renderOrder={order}
            position={position}
            rotation={rotation}
            scale={[planeWidth, planeHeight, 1]}
            hide={hide}
            url={window.assetUrl(`/images/misatoto/${img}.png`)}
          />
        );
      })}
    </group>
  );
};

const Kagura = ({ images, pos, rot, hide }) => {
  const [loadedTextures, setLoadedTextures] = useState([]);
  const group = useRef()

  useEffect(() => {
    textureLoader(images, setLoadedTextures)
  }, [images]);

  return (
    <group ref={group} position={pos} rotation={rot}>
      {loadedTextures.map((texture, index) => {
        const image = texture.image;
        const { position, rotation, img, size, order } = images[index];
        const aspectRatio = image.width / image.height;
        const planeHeight = size || 5;
        const planeWidth = planeHeight * aspectRatio;
        return (
          <Image
            key={index}
            name={img}
            renderOrder={order}
            position={position}
            rotation={rotation}
            scale={[planeWidth, planeHeight, 1]}
            hide={hide}
            url={window.assetUrl(`/images/misatoto/${img}.png`)}
          />
        );
      })}
    </group>
  );
};

function Canvas3D() {
  const images = [
    { img: 'cloud_2', position: [-25, 0, 88], rotation: [0, 0, 0], size: 10, order:0 },
    { img: 'cloud_1', position: [-20, 10, 89], rotation: [0, 0, 0], size: 10, order:0 },
    { img: 'cloud_large_left', position: [-10, -6, 86], rotation: [0, 0, 0], size: 10, order:0 },
    { img: 'cloud_large_right', position: [10, 8, 85], rotation: [0, 0, 0], size: 10, order:0 },
    { img: 'cloud_light_5', position: [25, -5, 83], rotation: [0, 0, 0], size: 10, order:0 },
    { img: 'cloud_1', position: [6, -5, 80], rotation: [0, 0, 0], size: 10, order:0 },
    { img: 'cloud_light_3', position: [-8, -4, 73.5], rotation: [0, 0, 0], size: 7.5, order:0 },
    { img: 'cloud_light_1', position: [-10, 3, 60], rotation: [0, 0, 0], size: 10, order:0 },
    { img: 'cloud_1', position: [6, -1, 55], rotation: [0, 0, 0], size: 6, order:0 },
  ];

  const images2 = [
    { img: 'map_20230403', position: [0, 0, 0], rotation: [0, 0, 0], size: 10, order:2 },
    { img: 'house_2', position: [0.1, 2, 0.1], rotation: [0, 0, 0], size: 2, order:3 },
    { img: 'cloud_light_5', position: [-3, -1.5, 3.5], rotation: [0, 0, 0], size: 1, order:2 },
    { img: 'cloud_light_2', position: [3, 2, 3.5], rotation: [0, 0, 0], size: 1, order:2 },
    // { img: 'cloud_1', position: [3, -1.5, 5], rotation: [0, 0, 0], size: 1, order:2 },
    { img: 'cloud_light_2', position: [-3, 2, 5], rotation: [0, 0, 0], size: 1, order:2 },
    // { img: 'cloud_2', position: [-8, 0, 5], rotation: [(Math.PI / 4), 0, 0], size: 3, order:2 },
    { img: 'cloud_light_5', position: [3, -5, 5], rotation: [(Math.PI / 4), 0, 0], size: 3, order:2 },
    { img: 'cloud_1', position: [-3, -3.5, 5], rotation: [0, 0, 0], size: 1, order:2 },
    { img: 'cloud_1', position: [3, 2, 4], rotation: [0, 0, 0], size: 0.5, order:2 },
  ];

  const images3 = [
    { img: 'hanadastore_shop', position: [-2, 2, -6], rotation: [0, 0, 0], size: 5, order:1 },
    { img: 'fujiya_all', position: [0.1, 1, -3], rotation: [0, 0, 0], size: 2, order:1 },
  ];

  const component = [
    { img: 'kagura_head', position: [0, 0, 0], rotation: [0, 0, 0], size: 1, order:1 },
    { img: 'kagura_body', position: [0, 0, 0], rotation: [0, 0, 0], size: 1, order:1 },
    { img: 'kagura_dragon', position: [0, 0, 0], rotation: [0, 0, 0], size: 1, order:1 },
    { img: 'kagura_hand_left', position: [0, 0, 0], rotation: [0, 0, 0], size: 1, order:1 },
    { img: 'kagura_hand_right', position: [0, 0, 0], rotation: [0, 0, 0], size: 1, order:1 },
  ];

  return (
    <Canvas style={{ background: '#efdbcb' }}>
      <ImageGroup id='story-1' pos={[0, 0, 0]} rot={[0, 0, 0]} images={images} hide />
      <ImageGroup id='story-2' pos={[0, -10, 70]} rot={[-(Math.PI / 2), 0, 0]} images={images2} />
      <ImageGroup id='story-3' pos={[0, -11, 70]} rot={[-(Math.PI / 2), 0, 0]} images={images3} />
      <Kagura id='component-1' pos={[0.5, -9.9, 72.5]} rot={[-(Math.PI / 2), 0, 0]} images={component} />
      <CameraController />
      {/* <OrbitControls /> */}
    </Canvas>
  );
}

export default Canvas3D;
