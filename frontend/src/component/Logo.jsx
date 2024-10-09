import React from 'react'
import logo from '../assets/logo.png'
import { useNavigate } from 'react-router-dom'

export default function Logo() {
  const navigate = useNavigate()
  const handleNavigate = () => {
    navigate('/sku')
  }
  return (
    <div onClick={handleNavigate} className=" cursor-pointer h-10 w-40 bg-cover bg-center" style={{ backgroundImage: `url(${logo})` }} />
  )
}
