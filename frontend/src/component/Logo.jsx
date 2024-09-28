import React from 'react'
import logo from '../assets/logo.png'

export default function Logo() {
  return (
    <div className="h-10 w-40 bg-cover bg-center" style={{ backgroundImage: `url(${logo})` }} />
  )
}
