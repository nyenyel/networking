import React, { useContext, useEffect, useState } from 'react'
import ReactFlow, { MiniMap, Controls, Background, useNodesState, useEdgesState, addEdge } from 'reactflow';
import 'reactflow/dist/style.css';
import LoginRedirect from '../context/LoginRedirect'
import { AppContext } from '../context/AppContext'
import Loading from '../component/Loading'
import AdminRedirect from '../context/AdminRedirect';
const nodeBuilding = (data, prevData, pos, layerHeight = 100, offset = 900, siblingOffset = 100) => {
  const nodes = prevData ? [prevData] : [];

  // Start with the x and y coordinates for positioning
  let tempX = pos.x ;
  let baseY = pos.y +100;

  // Array to track horizontal spacing between child nodes
  let childNodes = [];

  data.forEach((item, index) => {
      const name = `${item?.user?.last_name}, ${item?.user?.first_name} \n ${item?.user?.store_no}`;
      
      // Each node with a label, id, and style
      const node = {
          id: `${item?.user?.id}`,
          position: { x: tempX, y: baseY }, 
          data: { label: name },
          style: { backgroundColor: '#FF5722', color: 'white', border: '2px solid #FF9800' },
          width: 150,
          height: 42
      };
      
      // Add current node to the list of child nodes
      childNodes.push(node);
      tempX += node.width + siblingOffset; // Adjust horizontal position for siblings

      // If invited users exist, recursively create nodes for them
      if (item?.user?.invited_users && item?.user?.invited_users.length !== 0) {
          // Adjust x position so child nodes are centered under parent node
          const childPos = { x: tempX - node.width / 2, y: baseY + layerHeight };
          const invitedNodes = nodeBuilding(
              item.user.invited_users, 
              node, 
              childPos, 
              layerHeight, 
              offset, 
              siblingOffset
          );
          nodes.push(...invitedNodes);
          
          // Adjust tempX to avoid overlap with child nodes
          tempX = Math.max(tempX, invitedNodes[invitedNodes.length - 1].position.x + siblingOffset);
      }
  });

  // Add all child nodes to the overall nodes array
  nodes.push(...childNodes);

  return nodes;
};

const edgeBuilding = (source, data, prevData = []) => {
  const edges = prevData ? [...prevData] : [];
  
  data.forEach((item) => {
    const edge = {
      id: `e${source}-${item?.user?.id}`,
      source: `${source}`,
      target: `${item?.user?.id}`,
      type: 'default'
    };
    
    // Always push the current edge
    edges.push(edge);

    // If the user has invited users, recursively build edges for them
    if (item?.user?.invited_users && item?.user?.invited_users.length !== 0) {
      const recur = edgeBuilding(item?.user?.id, item?.user?.invited_users, edges);
      edges.push(...recur);
    }
  });

  return removeDuplicateEdges(edges);
};

// Function to remove duplicate edges by ID
const removeDuplicateEdges = (edges) => {
  const uniqueEdges = {};
  
  edges.forEach((edge) => {
    uniqueEdges[edge.id] = edge; // Use edge ID as key to ensure uniqueness
  });
  
  // Return the values of the uniqueEdges object as an array
  return Object.values(uniqueEdges);
};

export default function GenealogyOutlet() {
  const { token, apiClient, user } = useContext(AppContext);
  const [loading, setLoading] = useState(false);
  const [gene, setGene] = useState(null);
  const [geneVisualization, setGeneVisualization] = useState();
  const [geneConnection, setGeneConnection] = useState();
  
  const genealogy = async () => {
      try {
          setLoading(true);
          const response = await apiClient.get(`api/user/${user?.id}/with-invites`);
          setGene(response.data);
      } catch (e) {
          console.error(e.response);
      } finally {
          setLoading(false);
      }
  };

  useEffect(() => {
      if (user?.id) {
          genealogy();
      }
  }, [user?.id]);

  useEffect(() => {
      if (gene) {
        const pos = { x: 300, y: 0 }
        const name = `${gene?.last_name}, ${gene?.first_name} \n ${gene?.email}`;
        const firstNode = {
            id: `${gene?.id}`,
            position: pos,
            data: { label: name },
            style: { backgroundColor: '#88ea8b', color: 'white', border: '2px solid #ffffff' },
            width: 150,
            height: 42
        };
        const data = nodeBuilding(gene.invited_users, firstNode, pos);
        const edgeData = edgeBuilding(gene?.id, gene.invited_users)

        setGeneVisualization(data)
        setGeneConnection(edgeData)

        setNodes(data)
        setEdges(edgeData)
      }
  }, [gene]);


  const [nodes, setNodes, onNodesChange] = useNodesState(geneVisualization);
  const [edges, setEdges, onEdgesChange] = useEdgesState(geneConnection);
  const onConnect = (params) => setEdges((eds) => addEdge(params, eds));
  
  if (loading || !geneVisualization) {
      return (
          <>
              {token == null && <LoginRedirect />}
              {user?.admin == 1 && <AdminRedirect />}
              {loading && <Loading />}
          </>
      );
  }
  // console.log(nodes)
  return (
    <div className='flex w-full p-4 rounded-md h-screen bg-trc bg-opacity-20'>
        <ReactFlow
            nodes={nodes}
            edges={edges}
            onNodesChange={onNodesChange}
            onEdgesChange={onEdgesChange}
            nodesConnectable={false} // Disable node connection
            nodesDraggable={true} // Allow node dragging (optional)
            fitView
        >
            <MiniMap />
            <Controls />
            <Background />
        </ReactFlow>
    </div>
  );
}

const sample = () => {
  return [{
    id: '1',
    position: { x: 300, y: 50 },
    data: { label: 'Top Node' },
    style: { backgroundColor: '#FF5722', color: 'white', border: '2px solid #FF9800' }, // Custom color
  },
  {
    id: '2',
    position: { x: 300, y: 150 },
    data: { label: 'Level 2 - Node 1' },
    style: { backgroundColor: '#FF5722', color: 'white',  border: '2px solid #FF9800' }, // Custom color
  },
  {
    id: '3',
    position: { x: 500, y: 150 },
    data: { label: 'Level 2 - Node 2' },
    style: { backgroundColor: '#FF5722', color: 'black',  border: '2px solid #FF9800' }, // Custom color
  },
  {
    id: '4',
    position: { x: 300, y: 250 },
    data: { label: 'Level 3 - Node 1' },
    style: { backgroundColor: '#FF5722', color: 'black',  border: '2px solid #FF9800' }, // Custom color
  },
  {
    id: '5',
    position: { x: 500, y: 250 },
    data: { label: 'Level 3 - Node 2' },
    style: { backgroundColor: '#FF5722', color: 'white',  border: '2px solid #FF9800' }, // Custom color
  },
  {
    id: '6',
    position: { x: 700, y: 250 },
    data: { label: 'Level 3 - Node 3' },
    style: { backgroundColor: '#FF5722', color: 'white',  border: '2px solid #FF9800' }, // Custom color
  }]
}

const sampleEdge =() => {
  return [
    { id: 'e1-2', source: '1', target: '2', type: 'default' },
    { id: 'e1-3', source: '1', target: '3', type: 'default' },
    { id: 'e2-4', source: '2', target: '4', type: 'default' },
    { id: 'e2-5', source: '2', target: '5', type: 'default' },
    { id: 'e3-6', source: '3', target: '6', type: 'default' }
  ]
}