import React, { useContext, useEffect, useState } from 'react';
import { AppContext } from '../../context/AppContext';
import Loading from '../../component/Loading';
import UserRedirect from '../../context/UserRedirect';
import LoginRedirect from '../../context/LoginRedirect';



export default function DashboardOutlet() {
  const { apiClient, user, token } = useContext(AppContext);
  const [data, setData] = useState();
  const [loading, setLoading] = useState(false);
  const [isChecked ,setIsChecked] = useState(false)

  const getData = async () => {
    try {
      setLoading(true);
      const response = await apiClient.get('api/admin/dashboard');
      setData(response.data);
      if(response?.data?.switch === 1){
        setIsChecked(true)
      }
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

  const handleChange = (event) => {
    setIsChecked(event.target.checked); 
    updateSetting()
  };
  useEffect(() => {
    getData()
    // const channel = pusherClient.subscribe('dashboard')
    // channel.bind('update', (data) => {
    //   console.log('received data', data)
    // })

  }, []);

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
      {loading && <Loading />}
      {user?.admin === 0 && <UserRedirect />}
      <div className='flex flex-col w-full font-sf-extrabold gap-2'>
        <div className='flex-1 flex items-center'>
          <div className='flex-none bg-trc p-5'>ADMIN DASHBOARD</div>
          <div className={`${data?.isPointingSystemStopped ? 'bg-src': ' bg-green-600'} font-sf w-10 h-10 rounded-full mx-2 cursor-pointer`}></div>
          <div className='flex-1'/>
          <label className="inline-flex items-center gap-2 cursor-pointer">
            <input type="checkbox" className="sr-only peer" onChange={handleChange} checked={isChecked}/>
            <span className="ms-3 text-sm font-medium text-trc">Switch</span>
            <div className="relative w-11 h-6 bg-gray-200 rounded-full peer dark:bg-gray-700 peer-focus:ring-4 peer-focus:ring-teal-300 dark:peer-focus:ring-teal-800 peer-checked:after:translate-x-full rtl:peer-checked:after:-translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-0.5 after:start-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all dark:border-gray-600 peer-checked:bg-teal-600"></div>
          </label>
        </div>
        <div className='flex flex-col gap-2 text-src'>
          <div className='flex flex-wrap w-full gap-2'>
            <div className='flex-1 bg-trc p-5'>NEW MEMBERS<div>{data?.newMembers}</div></div>
            <div className='flex-1 bg-trc p-5'>TOTAL MEMBERS<div>{data?.totalMembers}</div></div>
            <div className='flex-1 bg-trc p-5'>TOTAL DAILY SALE<div>{data?.dailyPackageSales}</div></div>
            <div className='flex-1 bg-trc p-5'>DAILY PACKAGE SOLD<div>{data?.dailyDashboard?.package_sold}</div></div>
          </div>

          <div className='flex flex-wrap w-full gap-2'>
            <div className='flex-1 bg-trc p-5'>DAILY PRODUCT PURCHASED<div>{data?.dailyDashboard?.product_purchased}</div></div>
            <div className='flex-1 bg-trc p-5'>DAILY COMPANY REVENUE<div>{data?.dailyDashboard?.company_revenue}</div></div>
            <div className='flex-1 bg-trc p-5'>NO. OF OPEN STORE<div>{data?.openStores}</div></div>
          </div>

          <div className='flex flex-wrap w-full gap-2'>
            <div className='flex-1 h-full bg-trc p-5'>DAILY MEMBERS COMMISSION<div>{data?.dailyDashboard?.members_commission}</div></div>
          </div>

          <div className='flex flex-wrap w-full gap-2'>
            <div className='flex-1 bg-trc p-5'>WEEKLY PACKAGE SOLD<div>{data?.weeklyDashboard?.package_sold}</div></div>
            <div className='flex-1 bg-trc p-5'>WEEKLY PRODUCT PURCHASED<div>{data?.weeklyDashboard?.product_purchased}</div></div>
            <div className='flex-1 bg-trc p-5'>WEEKLY COMPANY REVENUE<div>{data?.weeklyDashboard?.company_revenue}</div></div>
            <div className='flex-1 bg-trc p-5'>WEEKL MEMBERS COMMISSION<div>{data?.weeklyDashboard?.members_commission}</div></div>
          </div>
        </div>
      </div>
    </>
  );
}
