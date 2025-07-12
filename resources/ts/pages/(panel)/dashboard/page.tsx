import PanelLayout from '~/components/Layouts/Panel';
import AnalyticDashboard from '../../../components/AnalyticDashboard';

const page = () => {
	return <AnalyticDashboard />;
};

page.layout = (page: any) => <PanelLayout children={page} />;
export default page;
