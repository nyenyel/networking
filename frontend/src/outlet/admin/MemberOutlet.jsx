import React, { useContext, useEffect, useState } from 'react'
import { AppContext } from '../../context/AppContext'
import Loading from '../../component/Loading'
import { formatDate } from '../TransactionOutlet'
import { useNavigate } from 'react-router-dom'
import { Box, Modal } from '@mui/material'
import LoginRedirect from '../../context/LoginRedirect'
import UserRedirect from '../../context/UserRedirect'

export default function MemberOutlet() {
  const {apiClient, token, user} = useContext(AppContext)
  const navigate = useNavigate()
  const [data, setData] = useState()
  const [subAccount, setSubAccount] = useState()
  const [oldMemberForm,setOldMemberForm] = useState()
  const [loading, setLoading] = useState(false)
  const [newMemberForm, setNewMemberForm] =useState()
  const [errorResponse, setErrorResponse] = useState()
  const [invCodeRes, setInvCodeRes] = useState()
  const [modalIsOpen, setModalIsOpen] = useState(false)
  const handleModal = () => setModalIsOpen(!modalIsOpen)

  const [oldMemberModalIsOpen, setOldMemberModalIsOpen] = useState(false)
  const handleOldMemberModal = () => {
    setOldMemberModalIsOpen(!oldMemberModalIsOpen)
    setOldMemberForm({})
    setSubAccount(null)
  }

  const addNewMember = async () => {
    try{
      setLoading(true)
      const response = await apiClient.post('api/test-invite-user', newMemberForm)
      console.log(response)
      navigate(0)
    } catch(error) {
        if (error.response) {
            console.error('Error Response Data:', error.response.data);
            setErrorResponse(error.response.data.errors)
            setInvCodeRes(error.response.data)
          } else if (error.request) {
            console.error('Error Request:', error.request);
          } else {
            console.error('Error Message:', error.message);
          } 
    } finally {
      setLoading(false)
      console.log(invCodeRes)
    } 
  }

  const addNewStore = async () => {
    try{
      setLoading(true)
      const response = await apiClient.post(`api/create-store-v2/${oldMemberForm.user_id}`, oldMemberForm)
      console.log(response)
      navigate(0)
    } catch(error) {
        if (error.response) {
            console.error('Error Response Data:', error.response.data);
            setErrorResponse(error.response.data.errors)
            setInvCodeRes(error.response.data)
          } else if (error.request) {
            console.error('Error Request:', error.request);
          } else {
            console.error('Error Message:', error.message);
          } 
    } finally {
      setLoading(false)
    } 
  }
  const handleChange = (e) => {
    const {name, value} = e.target
    setNewMemberForm({
        ...newMemberForm,
        [name]: value,
        photo_id: 'something'
    })
  }

  const handleChangeOldMember = (e) => {
    const {name, value} = e.target
    setOldMemberForm({
        ...oldMemberForm,
        [name]: value,
    })
  }

  const handleSubmit = (e) => {
    e.preventDefault()
    // console.log(newMemberForm)
    addNewMember()
  }

  const handleOldSubmit = (e) => {
    e.preventDefault()
    addNewStore()
  }

  const handleSubAccountButton = async () => {
    try {
      setLoading(true)
      const response = await apiClient.post('api/verify-email', oldMemberForm)
      setSubAccount(response.data.users)
      setOldMemberForm({
        user_id: response.data.users[0].id,
        email: response.data.users[0].email
      })
      setErrorResponse(null)
      console.log(response.data.users)
    } catch(error) {
      console.error('Error: ', error)
      setErrorResponse(error.response.data.errors)
    } finally{
      setLoading(false)
    }
  }
  const getUser = async () => {
    try{
      setLoading(true)
      const response = await apiClient.get('api/admin/user')
      setData(response.data)
    } catch(error) {
        if (error.response) {
            console.error('Error Response Data:', error.response.data);
            setErrorResponse(error.response.data.errors)
            console.log('error msg: ', error.response.data)
          } else if (error.request) {
            console.error('Error Request:', error.request);
          } else {
            console.error('Error Message:', error.message);
          } 
    } finally {
      setLoading(false)
    }
  }

  useEffect(()=>{
    getUser()
  }, [])
  return (
    <>
    {loading && <Loading />}
    {token == null && <LoginRedirect />}
    {user?.admin != 1 && <UserRedirect />}
    <Modal
        open={modalIsOpen}
        onClose={handleModal}
        aria-labelledby="modal-title"
        aria-describedby="modal-description"
        className="flex w-full z-20 justify-center items-center bg-black bg-opacity-50"
    >
        <Box className="bg-white rounded-lg shadow-lg text-def-t p-6">
            <h3 id="modal-title" className="font-semibold text-xl">New User</h3>
            <p id="modal-description" className="mb-4">This Popup is for adding a New Member.</p>
            <form onSubmit={handleSubmit}>
              <div className='flex gap-2'>
                <div className='flex flex-col'>
                  <label className="text-sm">First Name</label>
                  <input
                      type="text"
                      placeholder="Ex. 400"
                      name="first_name"
                      onChange={handleChange}
                      className="w-full p-2 rounded-md border-def-t border-opacity-5 border-2"
                  />
                  {errorResponse?.first_name && (
                      <div className="text-sm text-red-800 text-opacity-60">{errorResponse.first_name[0]}</div>
                  )}
                </div>
                <div className='flex flex-col'>
                  <label className="text-sm">Middle Name</label>
                  <input
                      type="text"
                      placeholder="Ex. 400"
                      name="middle_name"
                      onChange={handleChange}
                      className="w-full p-2 rounded-md border-def-t border-opacity-5 border-2"
                  />
                  
                </div>

                <div className='flex flex-col'>
                  <label className="text-sm">Last Name</label>
                  <input
                      type="text"
                      placeholder="Ex. 400"
                      name="last_name"
                      onChange={handleChange}
                      className="w-full p-2 rounded-md border-def-t border-opacity-5 border-2"
                  />
                  {errorResponse?.last_name && (
                      <div className="text-sm text-red-800 text-opacity-60">{errorResponse.last_name[0]}</div>
                  )}
                </div>

              </div>    
              <div className='flex gap-2'>
                <div className='flex-1 flex-col'>
                  <label className="text-sm">Username</label>
                  <input
                      type="text"
                      placeholder="Ex. 400"
                      name="username"
                      onChange={handleChange}
                      className="w-full p-2 rounded-md border-def-t border-opacity-5 border-2"
                  />
                  {errorResponse?.username && (
                      <div className="text-sm text-red-800 text-opacity-60">{errorResponse.username[0]}</div>
                  )}
                </div>
                <div className='flex-1 flex-col'>
                  <label className="text-sm">E-mail</label>
                  <input
                      type="text"
                      placeholder="Ex. 400"
                      name="email"
                      onChange={handleChange}
                      className="w-full p-2 rounded-md border-def-t border-opacity-5 border-2"
                  />
                  {errorResponse?.email && (
                      <div className="text-sm text-red-800 text-opacity-60">{errorResponse.email[0]}</div>
                  )}
                </div>
              </div>
                <label className="text-sm">Password</label>
                <br />
                <input
                    type="password"
                    placeholder="Ex. 400"
                    name="password"
                    onChange={handleChange}
                    className="w-full p-2 rounded-md border-def-t border-opacity-5 border-2"
                />
                {errorResponse?.password && (
                    <div className="text-sm text-red-800 text-opacity-60">{errorResponse.password[0]}</div>
                )}

                <label className="text-sm">Re-enter Password</label>
                <br />
                <input
                    type="password"
                    placeholder="Ex. 400"
                    name="password_confirmation"
                    onChange={handleChange}
                    className="w-full p-2 rounded-md border-def-t border-opacity-5 border-2"
                />
                {errorResponse?.password && (
                    <div className="text-sm text-red-800 text-opacity-60">{errorResponse.password[0]}</div>
                )}

                <label className="text-sm">Invitation Code (Optional)</label>
                <br />
                <input
                    type="text"
                    placeholder="Ex. 400"
                    name="invitation_code"
                    onChange={handleChange}
                    className="w-full p-2 rounded-md border-def-t border-opacity-5 border-2"
                />
                {invCodeRes && (
                    <div className="text-sm text-red-800 text-opacity-60">{invCodeRes?.message}</div>
                )}

                <button type="submit" className="bg-trc mt-2 bg-prc rounded-md py-2 w-full text-white">Add</button>
            </form>
        </Box>
    </Modal>

    <Modal
        open={oldMemberModalIsOpen}
        onClose={handleOldMemberModal}
        aria-labelledby="modal-title"
        aria-describedby="modal-description"
        className="flex w-full z-20 justify-center items-center bg-black bg-opacity-50"
    >
        <Box className="bg-white rounded-lg shadow-lg text-def-t p-6">
            <h3 id="modal-title" className="font-semibold text-xl">Add Store</h3>
            <p id="modal-description" className="mb-4">This Popup is for adding a Old Member.</p>
            <form onSubmit={handleSubmit}>
              <div className='flex gap-2 flex-col'>
                <div className='flex flex-col'>
                  <label className="text-sm">E-mail</label>
                  <div className='flex gap-2'>

                    <input
                      type="email"
                      placeholder="Ex. sampl@gmai.com"
                      name="email"
                      onChange={handleChangeOldMember}
                      className="w-full p-2 rounded-md border-def-t border-opacity-5 border-2"
                      />
                      <div onClick={handleSubAccountButton} className='bg-trc flex rounded-md cursor-pointer px-5 text-center content-center text-white p-2'>Verify</div>
                  </div>
                  {errorResponse?.email && (
                      <div className="text-sm text-red-800 text-opacity-60">{ errorResponse.email ||errorResponse.email[0] }</div>
                  )}
                </div>
                {subAccount && (
                  <div>
                    <div className='flex flex-col'>
                      <label className="text-sm">Store Invite from</label>
                      <select
                          type="text"
                          placeholder="Ex. 400"
                          name="user_id"
                          defaultValue={1}
                          onChange={handleChangeOldMember}
                          className="w-full p-2 rounded-md border-def-t border-opacity-5 border-2"
                      >
                        {subAccount?.map((item, index)=>(
                          <option key={index} value={item?.id}>Store Number: {item?.store_no}</option>
                        ))}
                      </select>
                      {errorResponse?.message && (
                        <div className="text-sm text-red-800 text-opacity-60">{ errorResponse.message }</div>
                      )}
                    </div>
                    <button onClick={handleOldSubmit} className="bg-trc mt-2 bg-prc rounded-md py-2 w-full text-white">Add</button>
                  </div>

                )}

              </div>
            </form>
        </Box>
    </Modal>
    <div className='flex flex-row gap-2'>
      <div onClick={handleModal} className='flex-none bg-trc font-sf-bold text-white p-4 mb-2 rounded-md hover:scale-101 cursor-pointer'>New Member</div>
      <div onClick={handleOldMemberModal} className='flex-none bg-trc font-sf-bold text-white p-4 mb-2 rounded-md hover:scale-101 cursor-pointer'>Old Member</div>
      <div className='flex-1 w-full'/>
    </div>
    <div className="relative overflow-x-auto shadow-md sm:rounded-lg font-sf">
      <table className="w-full text-sm text-left rtl:text-right text-black dark:text-gray-400">
          <caption className="p-5 text-lg font-semibold text-left rtl:text-right text-white bg-trc">
              Members
              <p className="mt-1 text-sm font-normal text-white ">This Table represents the current member the company have.</p>
          </caption>
          <thead className="text-xs uppercase bg-src bg-opacity-75 text-white ">
              <tr>
                  <th scope="col" className="px-6 py-3">
                      Full Name
                  </th>
                  <th scope="col" className="px-6 py-3">
                      Username
                  </th>
                  <th scope="col" className="px-6 py-3">
                      E-mail
                  </th>
                  <th scope="col" className="px-6 py-3">
                      Invite Code
                  </th>
                  <th scope="col" className="px-6 py-3">
                      Membership Date
                  </th>

              </tr>
          </thead>
          <tbody>
          {data?.map((item, index) => (
              <tr className="bg-white border-b " key={index}>
                      <th scope="row" className="px-6 py-4 font-medium text-black whitespace-nowrap">
                          {item?.last_name}, {item?.first_name}, {item?.middle_name.charAt(0)}.
                      </th>
                      <td className="px-6 py-4">
                          {item?.username}
                      </td>
                      <td className="px-6 py-4">
                        {item?.email}
                      </td>
                      <td className="px-6 py-4">
                        {item?.invite_code.code}
                      </td>
                      <td className="px-6 py-4">
                        {formatDate(item?.created_at)}
                      </td>
                  </tr>
          ))}
          </tbody>
      </table>
    </div>
    </>
  )
}
