import React, { useContext, useEffect, useState } from 'react';
import { AppContext } from '../../context/AppContext';
import Loading from '../../component/Loading';
import UserRedirect from '../../context/UserRedirect';
import LoginRedirect from '../../context/LoginRedirect';



export default function DashboardOutlet() {
  const { apiClient, user, token } = useContext(AppContext);
  const [data, setData] = useState();
  const [loading, setLoading] = useState(false);

  const getData = async () => {
    try {
      setLoading(true);
      const response = await apiClient.get('api/admin/dashboard');
      setData(response.data);
      console.log(response)
    } catch (error) {
      if (error.response) {
        console.error('Error Response Data:', error.response.data);
        console.log('error msg: ', error.response.data.message);
      } else if (error.request) {
        console.error('Error Request:', error.request);
      } else {
        console.error('Error Message:', error.message);
      }
    } finally {
      setLoading(false);
    }
  };

  const updateSetting = async () => {
    try {
      setLoading(true);
      const response = await apiClient.put('api/admin/update');
      console.log(response)
    } catch (error) {
      if (error.response) {
        console.error('Error Response Data:', error.response.data);
        console.log('error msg: ', error.response.data.message);
      } else if (error.request) {
        console.error('Error Request:', error.request);
      } else {
        console.error('Error Message:', error.message);
      }
    } finally {
      setLoading(false);
    }
  }
  useEffect(() => {
    getData()
    // const channel = pusherClient.subscribe('dashboard')
    // channel.bind('update', (data) => {
    //   console.log('received data', data)
    // })

  }, []);
  if (loading) {
    return <Loading />;
  }

  if (!data) {
    return (
      <div>
        {token == null && <LoginRedirect />}
        <Loading />
      </div>
    );
  }

  return (
    <>
      {user?.admin === 0 && <UserRedirect />}
      <div className='flex flex-col w-full font-sf-extrabold gap-2'>
        <div className='flex-1 flex items-center'>
          <div className='flex-none bg-trc p-5'>ADMIN DASHBOARD</div>
          <div onClick={updateSetting} className={`${data?.isPointingSystemStopped ? 'bg-green-600': ' bg-src'} font-sf w-10 h-10 rounded-full mx-2 cursor-pointer`}></div>
          <div className='flex-1' />
        </div>
        <div className='flex flex-col gap-2 text-src'>
          <div className='flex flex-wrap w-full gap-2'>
            <div className='flex-1 bg-trc p-5'>NEW MEMBERS<div>{data?.newMembers}</div></div>
            <div className='flex-1 bg-trc p-5'>TOTAL MEMBERS<div>{data?.totalMembers}</div></div>
            <div className='flex-1 bg-trc p-5'>TOTAL DAILY SALE<div>{data?.dailyPackageSales}</div></div>
            <div className='flex-1 bg-trc p-5'>DAILY PACKAGE SOLD<div>{data?.weeklyDashboard?.package_sold}</div></div>
          </div>

          <div className='flex flex-wrap w-full gap-2'>
            <div className='flex-1 bg-trc p-5'>DAILY PRODUCT PURCHASED<div>{data?.weeklyDashboard?.product_purchased}</div></div>
            <div className='flex-1 bg-trc p-5'>DAILY COMPANY REVENUE<div>{data?.weeklyDashboard?.company_revenue}</div></div>
            <div className='flex-1 bg-trc p-5'>NO. OF OPEN STORE<div>{data?.openStores}</div></div>
          </div>

          <div className='flex flex-wrap w-full gap-2'>
            <div className='flex-1 h-full bg-trc p-5'>DAILY MEMBERS COMMISSION<div>{data?.weeklyDashboard?.members_commission}</div></div>
          </div>
        </div>
      </div>
    </>
  );
}
