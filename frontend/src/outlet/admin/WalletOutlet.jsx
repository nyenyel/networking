import React, { useContext, useEffect, useState } from 'react'
import { AppContext } from '../../context/AppContext'
import LoginRedirect from '../../context/LoginRedirect'
import { formatDate } from '../TransactionOutlet'
import Loading from '../../component/Loading'
import { useNavigate } from 'react-router-dom'

export default function WalletOutlet() {
  const {user, apiClient, token} = useContext(AppContext)
  const [data, setData] = useState() 
  const [codeForm, setCodeForm] = useState() 
  const [message, setMessage] = useState() 
  const [loading,setLoading] = useState(false) 
  const navigate = useNavigate()
  const getData = async () => {
    setLoading(true)
    try {
      const resposne = await apiClient.get('api/wallet')
      setData(resposne.data)
    } catch (error) {
      console.error(error.resposne)
    } finally {
      setLoading(false)
    }
  }

  const handleCode =  async (e) =>{
    e.preventDefault()
    setLoading(true)
    try {
      const resposne = await apiClient.patch('api/transaction-approve', codeForm)
      navigate(0)
    } catch (error) {
      console.error("error: ", error?.response?.data.errors.code)
      setMessage(error?.response?.data.errors?.code[0])
    } finally {
      setLoading(false)
    }
  }

  const handleChange = (e) => {
    const {name, value} = e.target
    setCodeForm({
      ...codeForm,
      [name]: value
    })
  }


  useEffect(() => {
    getData()
  }, [])
  return (
    <>
    {user?.admin === 0 && <UserRedirect />}
    {token === null && <LoginRedirect/>}
    {loading && <Loading />}
      <div className='flex flex-col w-full font-sf-extrabold gap-2'>
        <div className='flex flex-col gap-2 text-src'>
          <div className='flex flex-wrap w-full gap-2'>
            <div className='flex-1 bg-trc p-5'>UNCLAIMED COMMISSION<div>{data?.unclaimed}</div></div>
            <div className='flex-1 bg-trc p-5'>WEEKLY COMMISION<div>{data?.weekly}</div></div>
          </div>
          <div className='bg-trc p-4 '>
            Enter Code to claim
            <form onSubmit={handleCode} className='mt-2 flex gap-2'>
              <div className='flex-1 flex flex-col'>
                
                <input onChange={handleChange} type='text' name='code' className='flex-1 rounded-md px-4 py-2 text-text' placeholder='Ex. JKAbfhacs213'/>
                {message && (
                  <div className="text-sm text-red-800 text-opacity-60">{ message }</div>
                )}
              </div>
              <button type='submit' className='bg-src text-white rounded-md text-sm font-normal px-5'>CLAIM CODE</button>
            </form>
          </div>
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
            {data?.transaction.map((item, index) => (
                <tr className="bg-white border-b " key={index}>
                        <th scope="row" className="px-6 py-4 font-medium text-black whitespace-nowrap">
                          {item?.users?.username}
                          
                        </th>
                        <td className="px-6 py-4">
                          {item?.amount}
                        </td>
                        <td className="px-6 py-4">
                          {item?.code}
                        </td>
                        <td className="px-6 py-4">
                          {item?.status === 0? 'UNCLAIMED':'CLAIMED'}
                        </td>
                        <td className="px-6 py-4">
                          {formatDate(item?.created_at)}
                        </td>
                    </tr>
            ))}
            </tbody>
          </table>
        </div>
      </div>
    </>
  )
}
