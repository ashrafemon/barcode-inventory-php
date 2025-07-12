import PanelLayout from '~/components/Layouts/Panel';
import CategoryBoard from '../../../components/CategoryBoard';

const page = () => {
	return <CategoryBoard />;
};

page.layout = (page: any) => <PanelLayout children={page} />;
export default page;
