import React, { useContext, useState } from 'react'
import { AppContext } from '../context/AppContext'
import { useNavigate } from 'react-router-dom'
import Warning from '../cards/Warning'
import LoginRedirect from '../context/LoginRedirect'
import UserRedirect from '../context/UserRedirect'
import Loading from '../component/Loading'

export default function LoginModule() {
  const {apiClient, token} = useContext(AppContext)
  const [loginForm, setLoginForm]= useState()
  const [loading, setLoading]= useState()
  const [message, setMessage] = useState(null)
  const navigate = useNavigate()

  const handleChange = (e) => {
    const{name, value }= e.target
    setLoginForm({
      ...loginForm,
      [name]: value
    })
  }
  const handleSubmit = async (e) => {
    e.preventDefault()
    setLoading(true)
    try{
      const response  = await apiClient.post('api/auth/login',loginForm)
      if(response.data.token){
        localStorage.setItem('token', response.data.token)
        navigate(0)
      }else{
        setMessage('Invalid Credentials');
      }
    } catch (e) {
        // Handle specific 422 error here
        setMessage('Invalid Credentials');
        console.error(e.response)
    } finally{
      setLoading(false)
    }
  }

  return (
    <>
    {token !=null && (<UserRedirect/>)}
    {loading && <Loading/>}
    <div className='absolute top-0 left-0 w-full h-full bg-trc bg-opacity-20 flex justify-center items-center'>
      <div className='flex bg-gradient-to-tr from-trc to-slate-300 p-8 rounded-md shadow-lg'>
        <form onSubmit={handleSubmit} className='flex flex-col space-y-4'>

          <label className='text-2xl font-sf-bold text-text select-none'>Login</label>
          {message && (<Warning data={message} />)}
          <input placeholder='E-mail' name='email' className='p-2 rounded border' onChange={handleChange}/>
          <input placeholder='Password' name='password' type='password' className='p-2 rounded border' onChange={handleChange}/>
          <button type='submit' className='p-2 bg-blue-500 text-white rounded'>Login</button>
        </form>
      </div>
    </div>
    </>
  )
}

