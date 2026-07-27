"use strict";
tsParticles.load("tsparticles", {
	// detectRetina: true,
	fpsLimit: 60,
	interactivity: {
		detectsOn: "canvas",
	},
	particles: {
		rotate: {
			value: 0,
			random: true,
			direction: "clockwise",
			animation: {
				enable: true,
				speed: 1,
				sync: false,
			},
		},
		move: {
			attract: {
				enable: false,
				rotateX: 600,
				rotateY: 1200,
			},
			bounce: false,
			direction: "none",
			enable: false,
			outMode: "bounce",
			random: false,
			speed: 0.2,
			straight: false,
		},
		number: {
			density: {
				enable: true,
				area: 800,
			},
			limit: 0,
			value: 20,
		},
		opacity: {
			animation: {
				enable: true,
				minimumValue: 0.05,
				speed: 0.3,
				sync: false,
			},
			random: true,
			value: 0.3,
		},
		shape: {
			image: [
				{
					src: "images/biology-icon/biology-01.png",
					width: 400,
					height: 400,
				},
				{
					src: "images/biology-icon/biology-02.png",
					width: 400,
					height: 400,
				},
				{
					src: "images/biology-icon/biology-03.png",
					width: 400,
					height: 400,
				},
				{
					src: "images/biology-icon/biology-04.png",
					width: 400,
					height: 400,
				},
				{
					src: "images/biology-icon/biology-05.png",
					width: 400,
					height: 400,
				},
				{
					src: "images/biology-icon/biology-06.png",
					width: 400,
					height: 400,
				},
				{
					src: "images/biology-icon/biology-07.png",
					width: 400,
					height: 400,
				},
				{
					src: "images/biology-icon/biology-08.png",
					width: 400,
					height: 400,
				},
				{
					src: "images/biology-icon/biology-09.png",
					width: 400,
					height: 400,
				},
				{
					src: "images/biology-icon/biology-10.png",
					width: 400,
					height: 400,
				},
			],
			type: "image",
		},
		size: {
			animation: {
				enable: false,
				minimumValue: 0.5,
				speed: 20,
				sync: false,
			},
			random: true,
			value: 35,
		},
	},
});
