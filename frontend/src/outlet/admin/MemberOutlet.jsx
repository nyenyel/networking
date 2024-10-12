import React, { useContext, useEffect, useState } from 'react'
import { AppContext } from '../../context/AppContext'
import Loading from '../../component/Loading'
import { formatDate } from '../TransactionOutlet'
import { useNavigate } from 'react-router-dom'
import { Box, Modal } from '@mui/material'

export default function MemberOutlet() {
  const {apiClient} = useContext(AppContext)
  const navigate = useNavigate()
  const [data, setData] = useState()
  const [loading, setLoading] = useState(false)
  const [newMemberForm, setNewMemberForm] =useState()
  const [errorResponse, setErrorResponse] = useState()
  const [invCodeRes, setInvCodeRes] = useState()
  const [modalIsOpen, setModalIsOpen] = useState(false)
  const handleModal = () => setModalIsOpen(!modalIsOpen)
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
            // if(error.response.data.message){
            //   console.log(error.response.data)
            // }
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
  const handleChange = (e) => {
    const {name, value} = e.target
    setNewMemberForm({
        ...newMemberForm,
        [name]: value,
        photo_id: 'something'
    })
  }

  const handleSubmit = (e) => {
    e.preventDefault()
    // console.log(newMemberForm)
    addNewMember()
  }

  const getUser = async () => {
    try{
      setLoading(true)
      const response = await apiClient.get('api/admin/user')
      setData(response.data)
    } catch(error) {
        if (error.response) {
            console.error('Error Response Data:', error.response.data);
            setApiResponse(error.response.data.message)
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
    <div className='flex flex-row'>
      <div onClick={handleModal} className='flex-none bg-trc font-sf-bold text-white p-4 mb-2 rounded-md hover:scale-101 cursor-pointer'>New Member</div>
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
