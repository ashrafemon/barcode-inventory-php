import React from 'react';
import Products from '../../../components/Products';
import PanelLayout from '~/components/Layouts/Panel';

const page = () => {
	return <Products />;
};

page.layout = (page: any) => <PanelLayout children={page} />;
export default page;
