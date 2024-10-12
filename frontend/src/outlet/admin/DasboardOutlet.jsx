import React, { useContext, useEffect, useState } from 'react'
import { AppContext } from '../../context/AppContext'
import Loading from '../../component/Loading'

export default function DasboardOutlet() {
  const {apiClient} = useContext(AppContext)
  const [data ,setData] = useState()
  const [loading, setLoading] = useState(false)

  const getData = async () => {
    try{
      setLoading(true)
      const response = await apiClient.get('api/admin/dashboard')
      setData(response.data)
    } catch(error) {
        if (error.response) {
            console.error('Error Response Data:', error.response.data);
            setApiResponse(error.response.data.message)
            console.log('error msg: ', error.response.data.message)
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
    getData()
  }, [])
  return (
    <>
    {loading && <Loading />}
    <div className='flex flex-col w-full font-sf-extrabold gap-2'>
      <div className='flex-1 flex'>
        <div className='flex-none bg-trc p-5 '>ADMIN DASHBOARD</div>
        <div className='flex-1'/>
      </div>
      <div className='flex flex-col gap-2 text-src'>
        <div className='flex flex-wrap w-full gap-2'>
          <div className='flex-1 bg-trc p-5'>NEW MEBERS<div>{data?.newMembers}</div></div>
          <div className='flex-1 bg-trc p-5'>TOTAL MEMBERS<div>{data?.totalMembers}</div></div>
          <div className='flex-1 bg-trc p-5'>TOTAL DAILY SALE<div>{data?.dailyPackageSales}</div></div>
          <div className='flex-1 bg-trc p-5'>DAILY PACKAGE SOLD<div>{data?.dailyPackageSales}x</div></div>
        </div>

        <div className='flex flex-wrap w-full gap-2'>
          <div className='flex-1 bg-trc p-5'>DAILY PRODUCT PURCHASED<div>{data?.dailyProductPurchased}</div></div>
          <div className='flex-1 bg-trc p-5'>DAILY COMPANY REVENUE<div>{data?.dailyCompanyRevenue}</div></div>
          <div className='flex-1 bg-trc p-5'>NO. OF OPEN STORE<div>{data?.openStores}</div></div>
          <div className='flex-1 bg-trc p-5'>NO. OF CLOSE STORE<div>{data?.graduatedStores}</div></div>
        </div>

        <div className='flex flex-wrap w-full gap-2'>
          <div className='flex-1 bg-trc p-5 gap-2'>
            <div className='flex flex-1 flex-row gap-1'>TOTAL WEEKLY COMPANY REVENUE:<div>{data?.newMembers}x</div></div>
            <div className='flex flex-1 flex-row gap-1'>TOTAL WEEKLY PRODUCT PURCHASED:<div>{data?.newMembers}x</div></div>
            <div className='flex flex-1 flex-row gap-1'>TOTAL WEEKLY MEMBER COMMISSION:<div>{data?.newMembers}x</div></div>
          </div>
          <div className='flex-none h-full bg-trc p-5'>DAILY MEMBERS COMMISSION<div>{data?.dailyMembersCommission}</div></div>
        </div>
      </div>

    </div>
    </>
  )
}
