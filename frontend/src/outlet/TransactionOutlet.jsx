import React, { useContext, useEffect, useState } from 'react'
import { AppContext } from '../context/AppContext'
import Loading from '../component/Loading';
import AdminRedirect from '../context/AdminRedirect';

export default function TransactionOutlet() {
    const{apiClient, user} = useContext(AppContext)
    const [data, setData] = useState()
    const [loading, setLoading] = useState(true);
    useEffect (() => {
        const getData = async () => {
            try {
                const response = await apiClient.get('api/transaction');
                setData(response.data);
                // console.log(response.data);
            } catch (e) {
                console.error('Error: ', e);
            } finally {
                setLoading(false); // Set loading to false once data is fetched
            }
        };
        getData();
    }, [])

    if (loading) {
        {user?.admin == 1 && <AdminRedirect />}
        return <Loading />; // Display a loading indicator
    }

    if (!data) {
        return <div>No data available</div>; // Display a message if there's no data
    }
    return (
        <>
        <div className="relative overflow-x-auto shadow-md sm:rounded-lg font-sf">
            <table className="w-full text-sm text-left rtl:text-right text-black dark:text-gray-400">
                <caption className="p-5 text-lg font-semibold text-left rtl:text-right text-white bg-trc">
                    Transaction
                    <p className="mt-1 text-sm font-normal text-white ">This Table represents the transaction you made.</p>
                </caption>
                <thead className="text-xs uppercase bg-src bg-opacity-75 text-white ">
                    <tr>
                        <th scope="col" className="px-6 py-3">
                            Transaction Type
                        </th>
                        <th scope="col" className="px-6 py-3">
                            Amount
                        </th>
                        <th scope="col" className="px-6 py-3">
                            Status
                        </th>
                        <th scope="col" className="px-6 py-3">
                            Code
                        </th>
                        <th scope="col" className="px-6 py-3">
                            Date
                        </th>

                    </tr>
                </thead>
                <tbody>
                {data?.map((item, index) => (
                    <tr className="bg-white border-b " key={index}>
                            <th scope="row" className="px-6 py-4 font-medium text-black whitespace-nowrap">
                                {item?.transaction.desc}
                            </th>
                            <td className="px-6 py-4">
                                {item?.amount}
                            </td>
                            <td className="px-6 py-4">
                                {item?.status === 1 ? 'Pending' :'Claimed'}
                            </td>
                            <td className="px-6 py-4">
                                {item?.code}
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

export const formatDate = (isoDateString) => {
    const date = new Date(isoDateString);
    const day = date.getDate();
    const month = date.toLocaleString('default', { month: 'short' }).toUpperCase(); // 'OCT'
    const year = date.getFullYear();
    
    return `${day} ${month} ${year}`;
};
