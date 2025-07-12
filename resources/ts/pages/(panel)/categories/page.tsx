import React from 'react';
import Categories from '../../../components/Categories';
import PanelLayout from '~/components/Layouts/Panel';

const page = () => {
	return <Categories />;
};

page.layout = (page: any) => <PanelLayout children={page} />;
export default page;
